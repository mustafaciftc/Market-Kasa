<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\SaleStatistic;
use App\Models\Return_Products;
use App\Models\User;
use App\Models\PaymentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\IyzicoService;
use App\Jobs\ProcessIyzicoPaymentJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use App\Models\Category;
use App\Models\RemoteReturn;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Session;

class SaleController extends Controller
{
    protected $iyzicoService;

    public function __construct(IyzicoService $iyzicoService)
    {
        $this->iyzicoService = $iyzicoService;
    }

    public function index()
    {
        try {
           $products = Product::where('active', 1)
    ->select('id', 'name', 'barcode', 'sell_price', 'stock_quantity')
    ->get()
    ->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'barcode' => $product->barcode,
            'sell_price' => $product->sell_price,
            'stock_quantity' => $product->stock_quantity,
        ];
    });
            $users = User::select('id', 'name')->get();
            $customers = Customer::select('id', 'name', 'phone')->get();
            $categories = Category::select('id', 'name')->get();

            \Log::info('Products fetched for index', [
                'products_count' => $products->count(),
                'images' => $products->pluck('image')->filter()->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in index method: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
        return view('satisyap', compact('products', 'users', 'customers', 'categories'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'basket' => 'required|json',
                'payment_type' => 'required|string|in:Nakit,Kart,Veresiye',
                'customer_id' => 'nullable|integer|exists:customers,id',
                '_token' => 'required'
            ]);

            $user = auth()->user();

            if ($validated['payment_type'] === 'Veresiye') {
                if (!$validated['customer_id']) {
                    throw new \Exception('Veresiye satış için müşteri seçimi zorunludur.');
                }

                if ($user->role === User::ROLE_PERSONNEL) {
                    $hasDebt = Debt::where('customer_id', $validated['customer_id'])
                                  ->where('amount', '>', 0)
                                  ->exists();
                    
                    if (!$hasDebt) {
                        throw new \Exception('Bu müşteri için veresiye hesabı bulunmamaktadır. Yeni veresiye hesabı sadece admin tarafından açılabilir.');
                    }
                }
            }

            $basketData = json_decode($request->basket, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($basketData)) {
                throw new \Exception('Geçersiz sepet verisi: ' . json_last_error_msg());
            }

            if (empty($basketData['items']) || !is_array($basketData['items'])) {
                throw new \Exception('Sepet boş veya geçersiz yapıda.');
            }

            DB::beginTransaction();

            $payTypeMap = ['Nakit' => 1, 'Kart' => 2, 'Veresiye' => 3];
            $payType = $payTypeMap[$validated['payment_type']] ?? 0;

            $sale = new Sale();
            $sale->user_id = $user->id;
            $sale->basket = json_encode($basketData['items']);
            $sale->customer_id = $validated['customer_id'];
            $sale->discount = $basketData['discount_percentage'] ?? 0;
            $sale->discount_fixed = $basketData['discount_fixed'] ?? 0;
            $sale->sub_total = $basketData['sub_total'] ?? 0;
            $sale->total_price = $basketData['total_price'] ?? 0;
            $sale->pay_type = $payType;
            $sale->discount_total = $basketData['discount_total'] ?? 0;
            $sale->save();

            $saleStatistic = new SaleStatistic();
            $saleStatistic->sale_id = $sale->id;
            $saleStatistic->customer_id = $validated['customer_id'];
            $saleStatistic->total_sell = $basketData['sub_total'] ?? 0;
            $saleStatistic->total_buy = 0;
            $saleStatistic->total_sale = 1;
            $saleStatistic->total_discount = $basketData['discount_total'] ?? 0;
            $saleStatistic->total_nakit = $validated['payment_type'] === 'Nakit' ? ($basketData['total_price'] ?? 0) : 0;
            $saleStatistic->total_kart = $validated['payment_type'] === 'Kart' ? ($basketData['total_price'] ?? 0) : 0;
            $saleStatistic->total_veresiye = $validated['payment_type'] === 'Veresiye' ? ($basketData['total_price'] ?? 0) : 0;
            $saleStatistic->save();

            if ($payType === 3 && $validated['customer_id']) {
                $existingDebt = Debt::where('customer_id', $validated['customer_id'])->first();
                if ($existingDebt) {
                    $existingDebt->amount += ($basketData['total_price'] ?? 0);
                    $existingDebt->description = 'Satış ID: ' . $sale->id . ' için veresiye eklendi/güncellendi';
                    $existingDebt->date = now();
                    $existingDebt->save();
                } else {
                    $debt = new Debt();
                    $debt->customer_id = $validated['customer_id'];
                    $debt->amount = $basketData['total_price'] ?? 0;
                    $debt->description = 'Satış ID: ' . $sale->id . ' için veresiye kaydı';
                    $debt->date = now();
                    $debt->save();
                }
            }

            $totalBuy = 0;
            foreach ($basketData['items'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Ürün bulunamadı: ID {$item['product_id']}");
                }
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Stok yetersiz: {$product->name} (Mevcut: {$product->stock_quantity}, Talep: {$item['quantity']})");
                }
                $product->stock_quantity -= $item['quantity'];
                $product->save();
                $totalBuy += ($product->buy_price * $item['quantity']);
            }

            $saleStatistic->total_buy = $totalBuy;
            $saleStatistic->save();

            $invoiceData = [
                'sale_id' => $sale->id,
                'created_at' => $sale->created_at ? $sale->created_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i'),
                'customer_name' => $sale->customer?->name ?? 'Müşteri Seçilmedi',
                'pay_type_text' => $validated['payment_type'],
                'items' => $basketData['items'],
                'sub_total' => $basketData['sub_total'] ?? 0,
                'discount_total' => $basketData['discount_total'] ?? 0,
                'total_price' => $basketData['total_price'] ?? 0
            ];

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Satış başarıyla tamamlandı.',
                'invoice' => $invoiceData
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama hatası: ' . implode(', ', array_merge(...array_values($e->errors()))),
                'errors' => $e->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Satış hatası: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Satış kaydedilirken hata: ' . $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function showSalesPage()
    {
        $products = Product::where('active', 1)
            ->select('id', 'name', 'barcode', 'sell_price', 'entry_date', 'expiry_date', 'stock_quantity')
            ->get();
        $customers = Customer::select('id', 'name', 'phone')->get();
        return view('sales.create', compact('products', 'customers'));
    }

    public function satisyap()
    {
        $products = Product::where('active', 1)
            ->select('id', 'name', 'barcode', 'sell_price', 'entry_date', 'expiry_date', 'stock_quantity', 'image')
            ->with('category')
            ->get()
            ->map(function ($product) {
                $imageExists = $product->image && Storage::disk('public')->exists($product->image);
                if (!$imageExists && $product->image) {
                    Log::warning('Image file missing for product', [
                        'product_id' => $product->id,
                        'image_path' => $product->image,
                    ]);
                }
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'sell_price' => $product->sell_price,
                    'entry_date' => $product->entry_date,
                    'expiry_date' => $product->expiry_date,
                    'stock_quantity' => $product->stock_quantity,
                    'image' => $imageExists ? asset('storage/' . $product->image) : asset('images/default.jpg'),
                    'category' => $product->category ? $product->category->name : 'N/A',
                ];
            });

        $users = User::select('id', 'name')->get();
        $customers = Customer::select('id', 'name', 'phone')->get();

        \Log::info('Products fetched for satisyap', [
            'products_count' => $products->count(),
            'images' => $products->pluck('image')->filter()->toArray(),
        ]);
        return view('satisyap', compact('products', 'users', 'customers'));
    }

    public function edit($id)
    {
        $sale = Sale::findOrFail($id);
        return view('satisislem', compact('sale'));
    }

    public function update(Request $request, $id)
    {
        try {
            $sale = Sale::findOrFail($id);

            $validated = $request->validate([
                'customerName' => 'nullable|string|max:255',
                'discount' => 'required|numeric|min:0',
                'totalPrice' => 'required|numeric|min:0',
                'payType' => 'required|in:1,2,3',
            ]);

            $sale->update([
                'discount' => $validated['discount'],
                'total_price' => $validated['totalPrice'],
                'pay_type' => $validated['payType'],
            ]);

            return response()->json(['success' => true, 'message' => 'Satış güncellendi.']);
        } catch (\Exception $e) {
            Log::error('Satış güncelleme hatası: ' . $e->getMessage(), [
                'sale_id' => $id,
                'request' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Satış güncellenirken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $sale = Sale::findOrFail($id);
            $sale->delete();
            Log::info('Sale deleted successfully', ['sale_id' => $id]);
            return redirect()->route('satisyonetim')->with('success', 'Satış başarıyla silindi.');
        } catch (\Exception $e) {
            Log::error('Satış silme hatası: ' . $e->getMessage(), ['sale_id' => $id]);
            return redirect()->route('satisyonetim')->with('error', 'Satış silinirken bir hata oluştu.');
        }
    }

    public function satisislem(Request $request)
    {
        $query = Sale::with(['customer', 'statistic', 'debt'])
            ->orderBy('created_at', 'desc');

        if ($request->has('sale_id')) {
            $query->where('id', $request->sale_id);
        }

        $sales = $query->get();

        $currentMonthTotal = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');
        
        $lastMonthTotal = Sale::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_price');
        
        $allTimeTotal = Sale::sum('total_price');
        
        $currentMonthDiscount = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('discount_total');
        
        $lastMonthDiscount = Sale::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('discount_total');
        
        $allTimeDiscount = Sale::sum('discount_total');
        
        $debtCustomers = Customer::whereHas('debts', function($query) {
            $query->where('amount', '>', DB::raw('COALESCE((SELECT SUM(amount) FROM payments WHERE payments.debt_id = debts.id), 0)'));
        })->with(['debts' => function($query) {
            $query->where('amount', '>', DB::raw('COALESCE((SELECT SUM(amount) FROM payments WHERE payments.debt_id = debts.id), 0)'))
                  ->with('payments');
        }])->get();
        
        return view('satisyonetim', compact(
            'sales', 
            'currentMonthTotal',
            'lastMonthTotal',
            'allTimeTotal',
            'currentMonthDiscount',
            'lastMonthDiscount',
            'allTimeDiscount',
            'debtCustomers'
        ));
    }

    public function satisyonetim(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : now()->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : now()->endOfDay();

        $sales = Sale::with(['customer', 'statistic', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->through(function ($sale) {
                $sale->basket = json_decode($sale->basket, true);
                return $sale;
            });

        $dailySales = Sale::with(['customer', 'user'])
            ->whereDate('created_at', now()->startOfDay())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sale) {
                $sale->basket = json_decode($sale->basket, true);
                return $sale;
            });

        $currentMonthTotal = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');
        $lastMonthTotal = Sale::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_price');
        $allTimeTotal = Sale::sum('total_price');
        $currentMonthDiscount = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('discount_total');
        $lastMonthDiscount = Sale::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('discount_total');
        $allTimeDiscount = Sale::sum('discount_total');
        $dailyTotal = Sale::whereDate('created_at', now()->startOfDay())
            ->sum('total_price');
        $dailyDiscount = Sale::whereDate('created_at', now()->startOfDay())
            ->sum('discount_total');

        return view('satisyonetim', compact(
            'sales',
            'dailySales',
            'currentMonthTotal',
            'lastMonthTotal',
            'allTimeTotal',
            'currentMonthDiscount',
            'lastMonthDiscount',
            'allTimeDiscount',
            'dailyTotal',
            'dailyDiscount'
        ));
    }

    public function getSaleDetails($id)
    {
        try {
            $sale = Sale::with(['customer', 'user'])->findOrFail($id);
            $payTypeText = match ($sale->pay_type) {
                1 => 'Nakit',
                2 => 'Kart',
                3 => 'Veresiye',
                4 => 'Banka Havalesi',
				5 => 'Kapıda Ödeme (Elden Ödeme)',
    			default => 'Bilinmiyor',            
			};

            $basketItems = [];
            if ($sale->basket) {
                try {
                    $basketItems = is_string($sale->basket) ? json_decode($sale->basket, true) : $sale->basket;
                    $basketItems = $basketItems ?: [];
                } catch (\Exception $e) {
                    Log::error("Basket decode error for sale ID {$id}: " . $e->getMessage());
                    $basketItems = [];
                }
            }

            return response()->json([
                'sale' => [
                    'id' => $sale->id,
                    'customer_name' => optional($sale->customer)->name ?? 'N/A',
                    'user_name' => optional($sale->user)->name ?? 'N/A',
                    'discount' => $sale->discount ?? 0,
                    'discount_fixed' => $sale->discount_fixed ?? 0,
                    'discount_total' => $sale->discount_total ?? 0,
                    'sub_total' => $sale->sub_total ?? 0,
                    'total_price' => $sale->total_price ?? 0,
                    'created_at' => $sale->created_at ? $sale->created_at->format('d.m.Y H:i') : 'N/A',
                ],
                'payTypeText' => $payTypeText,
                'basketItems' => $basketItems,
            ]);
        } catch (\Exception $e) {
            Log::error('Satış detayları alınamadı: ' . $e->getMessage(), ['sale_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Satış detayları alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getById(Request $request)
    {
        $product = Product::find($request->id);
        if (!$product) {
            return response()->json(['error' => 'Ürün bulunamadı'], 404);
        }
        $imageExists = $product->image && Storage::disk('public')->exists($product->image);
        if (!$imageExists && $product->image) {
            Log::warning('Image file missing for product', [
                'product_id' => $product->id,
                'image_path' => $product->image,
            ]);
        }
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sell_price' => $product->sell_price,
            'stock_quantity' => $product->stock_quantity,
            'barcode' => $product->barcode,
            'image' => $imageExists ? asset('storage/' . $product->image) : asset('images/default.jpg'),
        ]);
    }

    public function getByBarcode(Request $request)
    {
        $product = Product::where('barcode', $request->barcode)->first();
        if (!$product) {
            return response()->json(['error' => 'Barkod ile ürün bulunamadı'], 404);
        }
        $imageExists = $product->image && Storage::disk('public')->exists($product->image);
        if (!$imageExists && $product->image) {
            Log::warning('Image file missing for product', [
                'product_id' => $product->id,
                'image_path' => $product->image,
            ]);
        }
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'sell_price' => $product->sell_price,
            'stock_quantity' => $product->stock_quantity,
            'barcode' => $product->barcode,
            'image' => $imageExists ? asset('storage/' . $product->image) : asset('images/default.jpg'),
        ]);
    }

    public function returnProduct(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'reason' => 'required|string|max:255',
                'sale_id' => 'nullable|integer|exists:sales,id',
            ]);

            DB::beginTransaction();

            $product = Product::findOrFail($validated['product_id']);
            if ($product->stock_quantity < 0) {
                throw new \Exception('Ürün stoğu negatif olamaz.');
            }

            $return = new Return_Products();
            $return->product_id = $validated['product_id'];
            $return->quantity = $validated['quantity'];
            $return->reason = $validated['reason'];
            $return->sale_id = isset($validated['sale_id']) ? $validated['sale_id'] : null;
            $return->return_amount = $product->sell_price * $validated['quantity'];
            $return->date = now();
            $return->save();

            $product->stock_quantity += $validated['quantity'];
            $product->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'İade işlemi başarıyla tamamlandı.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz veri: ' . implode(', ', array_merge(...array_values($e->errors())))
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('İade hatası: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'İade işlemi sırasında hata: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchProducts(Request $request)
    {
        try {
            $query = Product::where('active', 1)
                ->select('id', 'name', 'sell_price', 'barcode', 'image');
            if ($request->term) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->term . '%')
                      ->orWhere('barcode', 'like', '%' . $request->term . '%');
                });
            }
            $products = $query->get()->map(function ($product) {
                $imageExists = $product->image && Storage::disk('public')->exists($product->image);
                if (!$imageExists && $product->image) {
                    Log::warning('Image file missing for product', [
                        'product_id' => $product->id,
                        'image_path' => $product->image,
                    ]);
                }
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sell_price' => $product->sell_price,
                    'barcode' => $product->barcode,
                    'image' => $imageExists ? asset('storage/' . $product->image) : asset('images/default.jpg'),
                ];
            });
            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Ürün arama hatası: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Ürün aranırken bir hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function listReturns(Request $request)
    {
        $query = Return_Products::with(['product', 'sale'])->orderBy('date', 'desc');

        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->productSearch) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->productSearch . '%')
                  ->orWhere('barcode', 'like', '%' . $request->productSearch . '%');
            });
        }

        $returns = $query->paginate(20);
        return view('urun-iade', compact('returns'));
    }

    public function getSaleProducts(Request $request)
    {
        try {
            $request->validate([
                'sale_id' => 'required|integer|exists:sales,id'
            ]);

            $sale = Sale::findOrFail($request->sale_id);
            $basket = json_decode($sale->basket, true) ?? [];

            $products = collect($basket)->map(function ($item) {
                return [
                    'product_id' => $item['product_id'],
                    'name' => $item['name'],
                    'barcode' => $item['barcode'] ?? 'Barkodsuz',
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'image' => isset($item['image']) && Storage::disk('public')->exists($item['image']) ? asset('storage/' . $item['image']) : asset('images/default.jpg'),
                ];
            });

            return response()->json($products);
        } catch (\Exception $e) {
            Log::error('Satış ürünleri alınamadı: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Satış ürünleri alınamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    public function details($id)
    {
        try {
            $sale = Sale::with(['customer', 'user'])->findOrFail($id);

            $basketItems = $sale->basket;

            if (is_string($basketItems)) {
                $decoded = json_decode($basketItems, true);
                $basketItems = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
            } elseif (!is_array($basketItems)) {
                $basketItems = [];
            }

            $basketItems = array_map(function ($item) {
                $imageExists = isset($item['image']) && Storage::disk('public')->exists($item['image']);
                if (!$imageExists && isset($item['image'])) {
                    Log::warning('Image file missing in basket item', [
                        'product_name' => $item['name'] ?? 'Unknown',
                        'image_path' => $item['image'],
                    ]);
                }
                return [
                    'name' => $item['name'] ?? 'Bilinmeyen Ürün',
                    'quantity' => $item['quantity'] ?? 0,
                    'price' => $item['price'] ?? 0,
                    'image' => $imageExists ? asset('storage/' . $item['image']) : asset('images/default.jpg'),
                ];
            }, $basketItems);

            return response()->json([
                'success' => true,
                'sale' => [
                    'id' => $sale->id,
                    'customer_name' => $sale->customer ? $sale->customer->name : 'Misafir',
                    'user_name' => $sale->user ? $sale->user->name : 'Bilinmiyor',
                    'sub_total' => $sale->sub_total,
                    'discount_total' => $sale->discount_total,
                    'total_price' => $sale->total_price,
                    'created_at' => $sale->created_at->format('d.m.Y H:i'),
                    'pay_type' => $sale->pay_type,
                ],
                'payTypeText' => match ($sale->pay_type) {
                    1 => 'Nakit',
                    2 => 'Kart',
                    3 => 'Veresiye',
					4 => 'Banka Havalesi',
                    5 => 'Kapıda Ödeme (Elden Ödeme)',
    				default => 'Bilinmiyor',
                },
                'basketItems' => $basketItems,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Satış detayları alınamadı: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroyReturn($id)
    {
        try {
            DB::beginTransaction();
            
            $return = Return_Products::findOrFail($id);
            
            if ($return->product) {
                $product = $return->product;
                $product->stock_quantity -= $return->quantity;
                $product->save();
            }
            
            if ($return->sale_id) {
                $statistic = SaleStatistic::where('sale_id', $return->sale_id)->first();
                if ($statistic) {
                    $statistic->total_return -= $return->quantity;
                    $statistic->total_return_amount -= $return->return_amount;
                    $statistic->save();
                }
            }
            
            if ($return->sale_id) {
                $sale = Sale::find($return->sale_id);
                if ($sale && $sale->pay_type == 3 && $sale->customer_id) {
                    $debt = Debt::where('sale_id', $sale->id)->first();
                    if ($debt) {
                        $debt->amount += $return->return_amount;
                        $debt->save();
                    }
                }
            }
            
            $return->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'İade kaydı başarıyla silindi.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('İade silme hatası: ' . $e->getMessage(), ['return_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'İade kaydı silinirken hata: ' . $e->getMessage()
            ], 500);
        }
    }

    public function filter(Request $request)
    {
        try {
            $query = Sale::with(['customer', 'user']);

            if ($request->start_date && $request->end_date) {
                $startDate = \Carbon\Carbon::createFromFormat('d.m.Y', $request->start_date)->startOfDay();
                $endDate = \Carbon\Carbon::createFromFormat('d.m.Y', $request->end_date)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            if ($request->pay_type) {
                $query->where('pay_type', $request->pay_type);
            }

            if ($request->customer) {
                $query->whereHas('customer', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->customer . '%');
                });
            }

            if ($request->user_id && auth()->user()->role === \App\Models\User::ROLE_ADMIN) {
                $query->where('user_id', $request->user_id);
            }

            $sales = $query->get()->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'customer' => $sale->customer ? ['name' => $sale->customer->name] : null,
                    'user' => $sale->user ? ['name' => $sale->user->name] : null,
                    'pay_type' => $sale->pay_type,
                    'discount_total' => $sale->discount_total,
                    'discount' => $sale->discount,
                    'discount_fixed' => $sale->discount_fixed,
                    'sub_total' => $sale->sub_total,
                    'total_price' => $sale->total_price,
                    'created_at' => $sale->created_at->toIso8601String(),
                ];
            });

            $summary = [
                'count' => $sales->count(),
                'total' => $sales->sum('total_price'),
                'discount' => $sales->sum('discount_total'),
                'cash' => $sales->where('pay_type', 1)->sum('total_price'),
                'card' => $sales->where('pay_type', 2)->sum('total_price'),
                'credit' => $sales->where('pay_type', 3)->sum('total_price'),
                'bank_transfer' => $sales->where('pay_type', 4)->sum('total_price'),
            ];

            return response()->json([
                'success' => true,
                'sales' => $sales,
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            \Log::error('Satış filtreleme hatası: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Filtreleme sırasında hata: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function checkCustomerDebt(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|integer|exists:customers,id'
            ]);

            $hasDebt = Debt::where('customer_id', $validated['customer_id'])->exists();

            return response()->json([
                'success' => true,
                'hasDebt' => $hasDebt
            ]);
        } catch (\Exception $e) {
            Log::error('Müşteri borç kontrol hatası: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Borç kontrolü sırasında hata: ' . $e->getMessage()
            ], 500);
        }
    }

    public function customerShopping()
    {
        $products = Product::where('active', 1)
            ->select('id', 'name', 'barcode', 'sell_price', 'stock_quantity', 'image')
            ->with('category')
            ->get()
            ->map(function ($product) {
                $imageExists = $product->image && Storage::disk('public')->exists($product->image);
                if (!$imageExists && $product->image) {
                    Log::warning('Image file missing for product', [
                        'product_id' => $product->id,
                        'image_path' => $product->image,
                    ]);
                }
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'sell_price' => $product->sell_price,
                    'stock_quantity' => $product->stock_quantity,
                    'image' => $imageExists ? asset('storage/' . $product->image) : asset('images/default.jpg'),
                    'category' => $product->category ? $product->category->name : 'N/A',
                ];
            });

        $categories = Category::select('id', 'name')->get();

        \Log::info('Customer shopping data', [
            'products_count' => $products->count(),
            'categories_count' => $categories->count(),
            'categories' => $categories->toArray(),
            'images' => $products->pluck('image')->filter()->toArray(),
        ]);

        return view('customer.shopping', compact('products', 'categories'));
    }

  public function getProductsByCategory(Request $request)
{
    try {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id'
        ]);
        $category = Category::findOrFail($validated['category_id']);
        $categoryName = trim($category->name);

        \Log::info('Fetching products for category', [
            'category_id' => $validated['category_id'],
            'category_name' => $categoryName
        ]);

        $products = Product::where('active', 1)
            ->where('category_id', $validated['category_id'])
            ->select('id', 'category_id', 'name', 'sell_price', 'stock_quantity', 'image')
            ->with('category') // Ensure the category relationship is loaded
            ->get()
            ->map(function ($product) {
                $imageExists = $product->image && Storage::disk('public')->exists($product->image);
                if (!$imageExists && $product->image) {
                    Log::warning('Image file missing for product', [
                        'product_id' => $product->id,
                        'image_path' => $product->image,
                    ]);
                }
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sell_price' => $product->sell_price,
                    'stock_quantity' => $product->stock_quantity,
                    'image' => $imageExists ? asset('storage/' . $product->image) : asset('images/default.jpg'),
                    'category' => optional($product->category)->name ?? 'N/A', // Safely access category name
                ];
            });

        \Log::info('Products retrieved', [
            'category_id' => $validated['category_id'],
            'products_count' => $products->count(),
            'products' => $products->toArray()
        ]);

        return response()->json($products, 200, [], JSON_UNESCAPED_UNICODE);
    } catch (\Exception $e) {
        \Log::error('Error fetching products by category: ' . $e->getMessage(), [
            'category_id' => $request->input('category_id'),
            'exception' => $e->getTraceAsString()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Ürünler yüklenemedi: ' . $e->getMessage()
        ], 500, [], JSON_UNESCAPED_UNICODE);
    }
}
	
  public function getCustomerCart(Request $request)
{
    $cart = $request->session()->get('customer_cart', [
        'items' => [],
        'sub_total' => 0,
        'discount_total' => 0,
        'total' => 0
    ]);

    if ($request->wantsJson()) {
        $cart['items'] = array_map(function ($item) {
            $imageExists = isset($item['image']) && Storage::disk('public')->exists($item['image']);
            if (!$imageExists && isset($item['image'])) {
                Log::warning('Image file missing in cart item', [
                    'product_id' => $item['product_id'] ?? 'Unknown',
                    'image_path' => $item['image'],
                ]);
            }
            $item['image'] = $imageExists ? asset('storage/' . $item['image']) : asset('images/default.jpg');
            return $item;
        }, $cart['items']);

        return response()->json([
            'success' => true,
            'cart' => $cart['items'],
            'total' => $cart['total']
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    return view('customer.cart', compact('cart'));
}
	
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        
        $product = Product::find($validated['product_id']);
        
        if ($product->stock_quantity < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => "Yeterli stok yok: {$product->name} (Mevcut: {$product->stock_quantity})"
            ], 400);
        }
        
        $cart = session()->get('customer_cart', [
            'items' => [], 
            'sub_total' => 0, 
            'discount_total' => 0, 
            'total' => 0
        ]);
        
        $itemKey = null;
        foreach ($cart['items'] as $key => $item) {
            if ($item['product_id'] == $product->id) {
                $itemKey = $key;
                break;
            }
        }
        
        $imageExists = $product->image && Storage::disk('public')->exists($product->image);
        if (!$imageExists && $product->image) {
            Log::warning('Image file missing for product', [
                'product_id' => $product->id,
                'image_path' => $product->image,
            ]);
        }

        if ($itemKey !== null) {
            $cart['items'][$itemKey]['quantity'] += $validated['quantity'];
            $cart['items'][$itemKey]['subtotal'] = $cart['items'][$itemKey]['price'] * $cart['items'][$itemKey]['quantity'];
        } else {
            $cart['items'][] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->sell_price,
                'quantity' => $validated['quantity'],
                'subtotal' => $product->sell_price * $validated['quantity'],
                'image' => $product->image
            ];
        }
        
        $this->updateCartTotals($cart);
        session()->put('customer_cart', $cart);
        session()->save();
        
        $cart['items'] = array_map(function ($item) {
            $imageExists = isset($item['image']) && Storage::disk('public')->exists($item['image']);
            $item['image'] = $imageExists ? asset('storage/' . $item['image']) : asset('images/default.jpg');
            return $item;
        }, $cart['items']);

        return response()->json([
            'success' => true,
            'message' => 'Ürün sepete eklendi',
            'cart' => $cart['items'],
            'total' => $cart['total'],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function removeFromCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);

        try {
            $cart = $request->session()->get('customer_cart', [
                'items' => [],
                'sub_total' => 0,
                'discount_total' => 0,
                'total' => 0
            ]);

            $cart['items'] = array_filter($cart['items'], fn($item) => $item['product_id'] != $validated['product_id']);

            $this->updateCartTotals($cart);
            $request->session()->put('customer_cart', $cart);

            $cart['items'] = array_map(function ($item) {
                $imageExists = isset($item['image']) && Storage::disk('public')->exists($item['image']);
                $item['image'] = $imageExists ? asset('storage/' . $item['image']) : asset('images/default.jpg');
                return $item;
            }, $cart['items']);

            return response()->json([
                'success' => true,
                'message' => 'Ürün sepetten çıkarıldı',
                'cart' => $cart['items'],
                'total' => $cart['total']
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('Sepetten çıkarma hatası: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Ürün sepetten çıkarılamadı: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:0'
        ]);

        try {
            $cart = $request->session()->get('customer_cart', [
                'items' => [],
                'sub_total' => 0,
                'discount_total' => 0,
                'total' => 0
            ]);

            if ($validated['quantity'] == 0) {
                $cart['items'] = array_filter($cart['items'], fn($item) => $item['product_id'] != $validated['product_id']);
            } else {
                $product = Product::select('id', 'name', 'sell_price', 'stock_quantity', 'image')
                    ->findOrFail($validated['product_id']);

                if ($product->stock_quantity < $validated['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Yeterli stok yok: {$product->name} (Mevcut: {$product->stock_quantity})"
                    ], 400);
                }

                $itemIndex = array_search($validated['product_id'], array_column($cart['items'], 'product_id'));

                $imageExists = $product->image && Storage::disk('public')->exists($product->image);
                if (!$imageExists && $product->image) {
                    Log::warning('Image file missing for product', [
                        'product_id' => $product->id,
                        'image_path' => $product->image,
                    ]);
                }

                if ($itemIndex !== false) {
                    $cart['items'][$itemIndex]['quantity'] = $validated['quantity'];
                    $cart['items'][$itemIndex]['subtotal'] = $cart['items'][$itemIndex]['price'] * $validated['quantity'];
                } else {
                    $cart['items'][] = [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'price' => (float) $product->sell_price,
                        'quantity' => $validated['quantity'],
                        'subtotal' => (float) $product->sell_price * $validated['quantity'],
                        'image' => $product->image
                    ];
                }
            }

            $this->updateCartTotals($cart);
            $request->session()->put('customer_cart', $cart);

            $cart['items'] = array_map(function ($item) {
                $imageExists = isset($item['image']) && Storage::disk('public')->exists($item['image']);
                $item['image'] = $imageExists ? asset('storage/' . $item['image']) : asset('images/default.jpg');
                return $item;
            }, $cart['items']);

            return response()->json([
                'success' => true,
                'message' => 'Sepet güncellendi',
                'cart' => $cart['items'],
                'total' => $cart['total']
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('Sepet güncelleme hatası: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Sepet güncellenemedi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateCartTotals(&$cart)
    {
        $cart['sub_total'] = array_sum(array_column($cart['items'], 'subtotal'));
        $cart['discount_total'] = 0;
        $cart['total'] = $cart['sub_total'] - $cart['discount_total'];
    }

    public function clearCart(Request $request)
    {
        try {
            $request->session()->forget('customer_cart');
            return response()->json([
                'success' => true,
                'message' => 'Sepet başarıyla temizlendi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sepet temizlenirken hata: ' . $e->getMessage()
            ], 500);
        }
    }

   public function checkout(Request $request)
    {
        $cart = $request->session()->get('customer_cart', [
            'items' => [],
            'sub_total' => 0,
            'discount_total' => 0,
            'total' => 0
        ]);
	   
	   $bankAccounts = BankAccount::where('is_active', true)->get();

        if (empty($cart['items'])) {
            return redirect()->route('customer.shopping')->with('error', 'Sepetiniz boş');
        }

        $cart['items'] = array_map(function ($item) {
            $imageExists = isset($item['image']) && Storage::disk('public')->exists($item['image']);
            $item['image'] = $imageExists ? asset('storage/' . $item['image']) : asset('images/default.jpg');
            return $item;
        }, $cart['items']);

		return view('customer.checkout', compact('cart', 'bankAccounts'));    }

  
        public function completeOrder(Request $request)
        {
            $startTime = microtime(true);
            Log::info('Starting completeOrder', [
                'user_id' => auth()->id(),
                'request' => $request->all(),
            ]);
        
            try {
                $validated = $request->validate([
                    'payment_method' => 'required|in:bank_transfer,cash_on_delivery,credit,credit_card',
                    'phone' => 'nullable|string|regex:/^[0-9]{10,15}$/', // Telefon opsiyonel
                    'shipping_address' => 'required|string|max:500',
                    'bank_receipt' => 'nullable|string|max:255|required_if:payment_method,bank_transfer',
                ]);
        
                $cart = $request->session()->get('customer_cart', [
                    'items' => [],
                    'sub_total' => 0,
                    'discount_total' => 0,
                    'total' => 0,
                ]);
        
                if (empty($cart['items'])) {
                    Log::warning('Cart is empty', [
                        'user_id' => auth()->id(),
                        'duration' => microtime(true) - $startTime,
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Sepetiniz boş',
                        'redirect_url' => route('customer.failure.generic')
                    ], 400);
                }
        
                $customer = Customer::where('email', auth()->user()->email)->first();
                if (!$customer) {
                    $customer = Customer::create([
                        'name' => auth()->user()->name,
                        'email' => auth()->user()->email,
                        'phone' => !empty($validated['phone']) ? $validated['phone'] : null, // Boş string kontrolü
                    ]);
                    Log::info('New customer created', [
                        'customer_id' => $customer->id,
                        'phone_provided' => !empty($validated['phone'])
                    ]);
                } elseif (isset($validated['phone']) && !empty($validated['phone']) && $customer->phone !== $validated['phone']) {
                    // Sadece telefon girilmişse ve mevcut telefondan farklıysa güncelle
                    $customer->phone = $validated['phone'];
                    $customer->save();
                    Log::info('Customer phone updated', [
                        'customer_id' => $customer->id,
                        'new_phone' => $validated['phone']
                    ]);
                } elseif (isset($validated['phone']) && empty($validated['phone'])) {
                    // Telefon alanı boş gönderildiyse null yap
                    $customer->phone = null;
                    $customer->save();
                    Log::info('Customer phone cleared', ['customer_id' => $customer->id]);
                }
        
                $surname = auth()->user()->surname ?? 'Unknown';
                if (empty($surname) || $surname === 'Unknown') {
                    Log::warning('Surname not found, using default', ['user_id' => auth()->id()]);
                }
        
                DB::beginTransaction();
        
                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'basket' => json_encode($cart['items']),
                    'customer_id' => $customer->id,
                    'sub_total' => $cart['sub_total'],
                    'discount_total' => $cart['discount_total'] ?? 0,
                    'total_price' => $cart['total'],
                    'pay_type' => $this->getPaymentType($validated['payment_method']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        
                if ($validated['payment_method'] === 'credit_card') {
                    $userData = [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => !empty($validated['phone']) ? $validated['phone'] : ($customer->phone ?? '0000000000'), // Fallback telefon
                        'surname' => $surname, 
                    ];
        
                    $iyzicoCart = [
                        'items' => array_map(function ($item) {
                            $product = Product::find($item['product_id']);
                            return [
                                'product_id' => $item['product_id'],
                                'name' => $item['name'],
                                'category' => $product ? $product->category->name ?? 'Genel' : 'Genel',
                                'price' => $item['price'],
                                'quantity' => $item['quantity'],
                            ];
                        }, $cart['items']),
                        'sub_total' => $cart['sub_total'],
                        'discount_total' => $cart['discount_total'],
                        'total' => $cart['total'],
                    ];
        
                    Log::info('Attempting to initialize Iyzico checkout form', [
                        'sale_id' => $sale->id,
                        'user_id' => auth()->id(),
                        'total_price' => $cart['total'],
                        'phone_provided' => !empty($validated['phone']),
                    ]);
        
                    $iyzicoResult = $this->initializeIyzicoCheckoutForm($sale, $iyzicoCart, $userData, $validated['shipping_address']);
        
                    if (!$iyzicoResult['success']) {
                        Log::error('Iyzico checkout form initialization failed', [
                            'sale_id' => $sale->id,
                            'error' => $iyzicoResult['message'] ?? 'Unknown error',
                            'iyzico_result' => $iyzicoResult,
                            'duration' => microtime(true) - $startTime,
                        ]);
                        throw new \Exception($iyzicoResult['message'] ?? 'Iyzico ödeme formu oluşturulamadı.');
                    }
        
                    PaymentDetail::create([
                        'sale_id' => $sale->id,
                        'payment_method' => 'credit_card',
                        'amount' => $cart['total'],
                        'details' => json_encode([
                            'method' => 'credit_card',
                            'status' => 'pending',
                            'iyzico_token' => $iyzicoResult['token'],
                            'phone_provided' => !empty($validated['phone']),
                        ]),
                    ]);
        
                    session()->put('iyzico_order', [
                        'order_id' => $sale->id,
                        'token' => $iyzicoResult['token'],
                        'user_id' => auth()->id(),
                        'session_id' => session()->getId(),
                    ]);
        
                    session()->put('user_auth_backup', [
                        'user_id' => auth()->id(),
                        'order_id' => $sale->id,
                        'created_at' => now(),
                    ]);
        
                    session()->save();
        
                    Log::info('Iyzico checkout form initialized successfully', [
                        'sale_id' => $sale->id,
                        'user_id' => auth()->id(),
                        'total_price' => $cart['total'],
                        'iyzico_token' => $iyzicoResult['token'],
                        'phone_provided' => !empty($validated['phone']),
                        'duration' => microtime(true) - $startTime,
                    ]);
        
                    DB::commit();
        
                    return response()->json([
                        'success' => true,
                        'message' => 'Ödeme formu oluşturuldu.',
                        'iyzico_form' => $iyzicoResult['html_content'],
                        'redirect_url' => null
                    ], 200);
                }
        
                if ($validated['payment_method'] === 'bank_transfer') {
                    PaymentDetail::create([
                        'sale_id' => $sale->id,
                        'payment_method' => 'bank_transfer',
                        'amount' => $cart['total'],
                        'details' => json_encode([
                            'method' => 'bank_transfer',
                            'status' => 'pending',
                            'bank_receipt' => $validated['bank_receipt'],
                            'phone_provided' => !empty($validated['phone']),
                        ]),
                    ]);
        
                    $request->session()->forget('customer_cart');
                    DB::commit();
        
                    Log::info('Bank transfer order completed', [
                        'sale_id' => $sale->id,
                        'user_id' => auth()->id(),
                        'total_price' => $cart['total'],
                        'phone_provided' => !empty($validated['phone']),
                        'duration' => microtime(true) - $startTime,
                    ]);
        
                    return response()->json([
                        'success' => true,
                        'message' => 'Siparişiniz banka havalesi ile alınmıştır. Onay için dekontunuzu kontrol edeceğiz.',
                        'redirect_url' => route('customer.success', ['sale_id' => $sale->id])
                    ], 200);
                }
        
                if ($validated['payment_method'] === 'cash_on_delivery') {
                    PaymentDetail::create([
                        'sale_id' => $sale->id,
                        'payment_method' => 'cash_on_delivery',
                        'amount' => $cart['total'],
                        'details' => json_encode([
                            'method' => 'cash_on_delivery',
                            'status' => 'pending',
                            'phone_provided' => !empty($validated['phone']),
                        ]),
                    ]);
        
                    $request->session()->forget('customer_cart');
                    DB::commit();
        
                    Log::info('Cash on delivery order completed', [
                        'sale_id' => $sale->id,
                        'user_id' => auth()->id(),
                        'total_price' => $cart['total'],
                        'phone_provided' => !empty($validated['phone']),
                        'duration' => microtime(true) - $startTime,
                    ]);
        
                    return response()->json([
                        'success' => true,
                        'message' => 'Kapıda ödeme siparişiniz alınmıştır.',
                        'redirect_url' => route('customer.success', ['sale_id' => $sale->id])
                    ], 200);
                }
        
                if ($validated['payment_method'] === 'credit') {
                    PaymentDetail::create([
                        'sale_id' => $sale->id,
                        'payment_method' => 'credit',
                        'amount' => $cart['total'],
                        'details' => json_encode([
                            'method' => 'credit',
                            'status' => 'pending',
                            'phone_provided' => !empty($validated['phone']),
                        ]),
                    ]);
        
                    $request->session()->forget('customer_cart');
                    DB::commit();
        
                    Log::info('Credit order completed', [
                        'sale_id' => $sale->id,
                        'user_id' => auth()->id(),
                        'total_price' => $cart['total'],
                        'phone_provided' => !empty($validated['phone']),
                        'duration' => microtime(true) - $startTime,
                    ]);
        
                    return response()->json([
                        'success' => true,
                        'message' => 'Veresiye ile ödeme siparişiniz alınmıştır.',
                        'redirect_url' => route('customer.success', ['sale_id' => $sale->id])
                    ], 200);
                }
        
                throw new \Exception('Geçersiz ödeme yöntemi.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Order completion error: ' . $e->getMessage(), [
                    'user_id' => auth()->id(),
                    'request' => $request->all(),
                    'trace' => $e->getTraceAsString(),
                    'duration' => microtime(true) - $startTime,
                ]);
                $saleId = isset($sale) ? $sale->id : null;
                return response()->json([
                    'success' => false,
                    'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage(),
                    'redirect_url' => $saleId ? route('customer.failure', ['sale_id' => $saleId]) : route('customer.failure.generic')
                ], 500);
            }
        }
private function initializeIyzicoCheckoutForm($sale, $cart, $userData, $shippingAddress)
    {
        try {
            $result = $this->iyzicoService->initializeCheckoutForm($sale, $cart, $userData, $shippingAddress);
         if (!$result['success']) {
    $errorMessage = $result['message'] ?? 'Bilinmeyen bir hata oluştu';
    Log::error('Iyzico checkout form initialization failed', [
        'sale_id' => $sale->id,
        'error' => $errorMessage,
        'iyzico_response' => $result,
    ]);
    throw new \Exception('Iyzico ödeme formu oluşturulamadı: ' . $errorMessage);
}
            return $result;
        } catch (\Exception $e) {
            Log::error('Iyzico checkout form initialization exception', [
                'sale_id' => $sale->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
	
public function paymentCallback(Request $request)
{
    $token = $request->input('token') ?? $request->query('token');
    $conversationId = $request->input('conversationId') ?? $request->query('conversationId');

    if (!$conversationId) {
        $iyzicoOrder = session()->get('iyzico_order', []);
        if (!empty($iyzicoOrder) && $iyzicoOrder['token'] === $token) {
            $conversationId = $iyzicoOrder['order_id'] ? 'sale-' . $iyzicoOrder['order_id'] : null;
        }
    }

    if (!$token || !$conversationId) {
        Log::error('Invalid callback: Missing token or conversationId', [
            'request' => $request->all(),
            'query' => $request->query(),
            'duration' => microtime(true) - $startTime,
        ]);
        return response()->json(['success' => false, 'error' => 'Ödeme bilgisi eksik veya geçersiz.'], 400);
    }

    $startTime = microtime(true);
    Log::info('Payment Callback Received', [
        'method' => $request->method(),
        'url' => $request->fullUrl(),
        'headers' => $request->headers->all(),
        'input' => $request->all(),
        'query' => $request->query(),
        'ip' => $request->ip(),
    ]);

    $iyzicoOrder = session()->get('iyzico_order', []);
    if (empty($iyzicoOrder) || $iyzicoOrder['token'] !== $token) {
        $paymentDetail = PaymentDetail::whereJsonContains('details->iyzico_token', $token)->first();
        if (!$paymentDetail) {
            Log::error('Invalid token: Token not found in session or database', [
                'token' => $token,
                'conversationId' => $conversationId,
                'duration' => microtime(true) - $startTime,
            ]);
            return response()->json(['success' => false, 'error' => 'Geçersiz token.'], 400);
        }
    }

    try {
        $iyzicoService = app(IyzicoService::class);
        $paymentResult = $iyzicoService->verifyPayment($token, $conversationId);

        if ($paymentResult['success']) {
            $saleId = str_replace('sale-', '', $conversationId);
            $sale = Sale::find($saleId);

            if (!$sale) {
                Log::error('Sale not found for callback', [
                    'sale_id' => $saleId,
                    'token' => $token,
                    'conversationId' => $conversationId,
                    'duration' => microtime(true) - $startTime,
                ]);
                return response()->json(['success' => false, 'error' => 'Sipariş bulunamadı.'], 404);
            }

            DB::beginTransaction();

            $paymentDetail = PaymentDetail::where('sale_id', $sale->id)->first();
            if ($paymentDetail) {
                $details = json_decode($paymentDetail->details, true) ?? [];
                $details = array_merge($details, [
                    'payment_id' => $paymentResult['payment_id'],
                    'status' => 'completed',
                    'item_transactions' => $paymentResult['item_transactions'] ?? [], // Fallback to empty array
                    'verified_at' => now()->toDateTimeString(),
                ]);
                $paymentDetail->details = json_encode($details);
                $paymentDetail->save();
            } else {
                throw new \Exception('Ödeme detayı bulunamadı.');
            }

            $basket = json_decode($sale->basket, true) ?? [];
            foreach ($basket as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    if ($product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Yeterli stok yok: {$product->name}");
                    }
                    $product->stock_quantity -= $item['quantity'];
                    $product->save();
                } else {
                    throw new \Exception("Ürün bulunamadı: ID {$item['product_id']}");
                }
            }

            DB::commit();

            session()->forget('iyzico_order');
            session()->forget('user_auth_backup');

            Log::info('Payment processed successfully', [
                'sale_id' => $saleId,
                'token' => $token,
                'payment_id' => $paymentResult['payment_id'],
                'duration' => microtime(true) - $startTime,
            ]);

            return redirect()->route('customer.success', ['sale_id' => $saleId]);
        }

        Log::error('Payment verification failed', [
            'token' => $token,
            'conversationId' => $conversationId,
            'error' => $paymentResult['message'] ?? 'Unknown error',
            'duration' => microtime(true) - $startTime,
        ]);
        return redirect()->route('customer.failure', ['sale_id' => str_replace('sale-', '', $conversationId)])
            ->with('error', $paymentResult['message'] ?? 'Ödeme doğrulanamadı.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Payment callback exception', [
            'token' => $token,
            'conversationId' => $conversationId,
            'exception' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'duration' => microtime(true) - $startTime,
        ]);
        return redirect()->route('customer.failure', ['sale_id' => str_replace('sale-', '', $conversationId)])
            ->with('error', 'Ödeme işlemi sırasında bir hata oluştu: ' . $e->getMessage());
    }
}
	
public function paymentSuccess($sale_id)
{
    $sale = Sale::findOrFail($sale_id);
    Log::info('Payment success page accessed', [
        'sale_id' => $sale_id,
        'user_id' => auth()->id(),
    ]);

    $payTypeText = match ($sale->pay_type) {
        1 => 'Nakit',
        2 => 'Kart',
        3 => 'Veresiye',
        4 => 'Banka Havalesi',
        5 => 'Kapıda Ödeme (Elden Ödeme)',
        default => 'Bilinmiyor',
    };

    return view('customer.success', compact('sale', 'payTypeText'));
}
	
public function paymentStatus($sale_id)
{
    $sale = Sale::find($sale_id);
    if (!$sale) {
        return response()->json(['status' => 'error', 'message' => 'Sale not found'], 404);
    }

    $paymentDetail = PaymentDetail::where('sale_id', $sale_id)->first();
    if ($paymentDetail && isset(json_decode($paymentDetail->details, true)['payment_id'])) {
        return response()->json(['payment_id' => json_decode($paymentDetail->details, true)['payment_id']]);
    }

    return response()->json(['payment_id' => null]);
} 

public function paymentPending($sale_id)
{
    $sale = Sale::findOrFail($sale_id);
    if (!in_array($sale->pay_type, [4, 5]) || $sale->status !== 'pending') {
        Log::warning('Invalid access to payment pending page', [
            'sale_id' => $sale_id,
            'pay_type' => $sale->pay_type,
            'status' => $sale->status,
        ]);
        return redirect()->route('customer.orders')->with('error', 'Geçersiz sipariş durumu.');
    }

    return view('customer.pending', compact('sale'));
}
	
 public function paymentFailure($sale_id)
    {
        $sale = Sale::findOrFail($sale_id);
        \Log::info('Payment failure page accessed', [
            'sale_id' => $sale_id,
            'user_id' => auth()->id(),
        ]);

        return view('customer.failure', compact('sale'));
    }

public function paymentFailureGeneric()
{
    return view('customer.failure_generic');
}
	
	public function paymentDetails($saleId)
{
    try {
        $sale = Sale::with('customer')->findOrFail($saleId);
        $basketItems = json_decode($sale->basket, true);

		 $payTypeText = match ($sale->pay_type) {
		2 => 'Kredi Kartı',
		3 => 'Veresiye',
		4 => 'Banka Havalesi',
		5 => 'Kapıda Ödeme (Elden Ödeme)',
		default => 'Bilinmiyor',
	};

        $items = array_map(function ($item) {
            return [
                'product_id' => $item['product_id'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ];
        }, $basketItems);

        return response()->json([
            'success' => true,
            'sale' => [
                'id' => $sale->id,
                'customer_name' => $sale->customer->name,
                'total_price' => $sale->total_price,
                'created_at' => $sale->created_at->format('d.m.Y H:i'),
            ],
            'basketItems' => $items,
            'payTypeText' => $payTypeText,
        ]);
    } catch (\Exception $e) {
        Log::error('Error fetching sale details: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Sipariş detayları alınamadı.',
        ], 500);
    }
}

 private function getPaymentType($method)
    {
      switch ($method) {
        case 'credit_card':
            return 2;
        case 'credit':
            return 3;
        case 'bank_transfer':
            return 4;
        case 'cash_on_delivery':
            return 5;
        default:
            return 0;
    	}
    }

    public function getCsrfToken()
    {
        return response()->json([
            'csrf_token' => csrf_token(),
        ]);
    }
	
public function customerOrders(Request $request)
{
    $sales = Sale::where('user_id', Auth::id())
                 ->orderBy('created_at', 'desc')
                 ->get();
    Log::info('Customer orders fetched', [
        'user_id' => Auth::id(),
        'sales_count' => $sales->count(),
        'sale_ids' => $sales->pluck('id')->toArray(),
    ]);
    return view('customer.orders', compact('sales'));
}

    public function createStorageLink()
    {
        try {
            $linkExists = is_link(public_path('storage'));
            if ($linkExists) {
                \Log::info('Storage link already exists');
                return response()->json([
                    'success' => true,
                    'message' => 'Storage link zaten mevcut.',
                ]);
            }

            Artisan::call('storage:link');
            $output = Artisan::output();
            \Log::info('Storage link created', ['output' => $output]);
            return response()->json([
                'success' => true,
                'message' => 'Storage link oluşturuldu.',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            \Log::error('Storage link creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Storage link oluşturulamadı: ' . $e->getMessage(),
            ], 500);
        }
    }
	
public function remoteProductDetails($id)
{
    try {
        $sale = Sale::with(['customer', 'user'])->findOrFail($id);
        $basketItems = [];
        if (!empty($sale->basket)) {
            $decoded = json_decode($sale->basket, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $basketItems = $decoded;
            } else {
                Log::warning('Invalid JSON in basket', [
                    'sale_id' => $id,
                    'basket' => $sale->basket,
                    'json_error' => json_last_error_msg(),
                ]);
            }
        } else {
            Log::warning('Empty or null basket', ['sale_id' => $id]);
        }

        $basketItems = array_map(function ($item) {
            $imageExists = isset($item['image']) && Storage::disk('public')->exists($item['image']);
            if (!$imageExists && isset($item['image'])) {
                Log::warning('Image file missing in basket item', [
                    'product_name' => $item['name'] ?? 'Unknown',
                    'image_path' => $item['image'],
                ]);
            }
            return [
                'name' => $item['name'] ?? 'Bilinmeyen Ürün',
                'quantity' => $item['quantity'] ?? 0,
                'price' => $item['price'] ?? 0,
                'image' => $imageExists ? asset('storage/' . $item['image']) : asset('images/default.jpg'),
            ];
        }, $basketItems);

        return response()->json([
            'success' => true,
            'sale' => [
                'id' => $sale->id,
                'customer_name' => $sale->customer ? $sale->customer->name : 'Misafir',
                'user_name' => $sale->user ? $sale->user->name : 'Bilinmiyor',
                'sub_total' => $sale->sub_total,
                'discount_total' => $sale->discount_total,
                'total_price' => $sale->total_price,
                'created_at' => $sale->created_at->format('d.m.Y H:i'),
                'pay_type' => $sale->pay_type,
            ],
            'payTypeText' => match ($sale->pay_type) {
                1 => 'Nakit',
                2 => 'Kart',
                3 => 'Veresiye',
                4 => 'Banka Havalesi',
			    5 => 'Kapıda Ödeme (Elden Ödeme)',
                default => 'Bilinmiyor',
            },
            'basketItems' => $basketItems,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    } catch (\Exception $e) {
        Log::error('Satış detayları alınamadı: ' . $e->getMessage(), [
            'sale_id' => $id,
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Satış detayları alınamadı: ' . $e->getMessage(),
        ], 500, [], JSON_UNESCAPED_UNICODE);
    }
}

public function getRemoteSaleProducts(Request $request)
{
    try {
        $request->validate([
            'sale_id' => 'required|integer|exists:sales,id'
        ]);
        $sale = Sale::findOrFail($request->sale_id);
        $basket = [];
        if (!empty($sale->basket)) {
            $decoded = json_decode($sale->basket, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $basket = $decoded;
            } else {
                Log::warning('Invalid JSON in basket', [
                    'sale_id' => $request->sale_id,
                    'basket' => $sale->basket,
                    'json_error' => json_last_error_msg(),
                ]);
            }
        } else {
            Log::warning('Empty or null basket', ['sale_id' => $request->sale_id]);
        }

        $products = collect($basket)->map(function ($item) {
            return [
                'product_id' => $item['product_id'] ?? 0,
                'name' => $item['name'] ?? 'Bilinmeyen Ürün',
                'barcode' => $item['barcode'] ?? 'Barkodsuz',
                'price' => $item['price'] ?? 0,
                'quantity' => $item['quantity'] ?? 0,
                'image' => isset($item['image']) && Storage::disk('public')->exists($item['image']) ? asset('storage/' . $item['image']) : asset('images/default.jpg'),
            ];
        })->filter(function ($item) {
            return $item['product_id'] > 0; 
        })->values()->all();

        return response()->json($products, 200, [], JSON_UNESCAPED_UNICODE);
    } catch (\Exception $e) {
        Log::error('Satış ürünleri alınamadı: ' . $e->getMessage(), [
            'sale_id' => $request->sale_id,
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Satış ürünleri alınamadı: ' . $e->getMessage()
        ], 500, [], JSON_UNESCAPED_UNICODE);
    }
}
	

public function requestRemoteReturn(Request $request)
{
    try {
        $validated = $request->validate([
            'sale_id' => 'required|integer|exists:sales,id',
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $sale = Sale::findOrFail($validated['sale_id']);
        if ($sale->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bu sipariş size ait değil.'
            ], 403);
        }

        $existingReturn = RemoteReturn::where('sale_id', $validated['sale_id'])
            ->where('product_id', $validated['product_id'])
            ->where('status', 'approved')
            ->first();

        if ($existingReturn) {
            return response()->json([
                'success' => false,
                'message' => 'Bu ürün zaten iade edilmiş. Tekrar iade talebi oluşturamazsınız.'
            ], 400);
        }

        $basket = json_decode($sale->basket, true) ?? [];
        $item = collect($basket)->firstWhere('product_id', $validated['product_id']);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Bu ürün siparişte bulunamadı.'
            ], 404);
        }

        if ($item['quantity'] < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'İade miktarı sipariş miktarını aşıyor.'
            ], 400);
        }

        $product = Product::findOrFail($validated['product_id']);

        DB::beginTransaction();

        $customer = Customer::where('email', auth()->user()->email)->first();
        if (!$customer) {
            $customer = Customer::create([
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => auth()->user()->phone ?? null,
            ]);
        }

        $remoteReturn = new RemoteReturn();
        $remoteReturn->sale_id = $validated['sale_id'];
        $remoteReturn->product_id = $validated['product_id'];
        $remoteReturn->customer_id = $customer->id;
        $remoteReturn->quantity = $validated['quantity'];
        $remoteReturn->reason = $validated['reason'];
        $remoteReturn->return_amount = $product->sell_price * $validated['quantity'];
        $remoteReturn->status = 'pending';
        $remoteReturn->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'İade talebi başarıyla oluşturuldu.'
        ], 200, [], JSON_UNESCAPED_UNICODE);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Geçersiz veri: ' . implode(', ', array_merge(...array_values($e->errors())))
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Remote return request error: ' . $e->getMessage(), ['request' => $request->all()]);
        return response()->json([
            'success' => false,
            'message' => 'İade talebi oluşturulurken hata: ' . $e->getMessage()
        ], 500);
    }
}
public function otherSales(Request $request)
    {
        try {
            $customerIds = User::where('role', 'customer')->pluck('id');
            $sales = Sale::whereIn('user_id', $customerIds)
                         ->with('user')
                         ->orderBy('created_at', 'desc')
                         ->get();
            Log::info('Other sales fetched', [
                'user_id' => Auth::id(),
                'sales_count' => $sales->count(),
                'sale_ids' => $sales->pluck('id')->toArray(),
            ]);
            return view('other-sales', compact('sales'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch other sales: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Satışlar yüklenemedi.');
        }
    }
	
    public function updateOtherSale(Request $request, $id)
    {
        try {
            $request->validate([
                'total_price' => 'required|numeric|min:0',
                'pay_type' => 'required|integer|in:1,2,3,4',
                'basket' => 'nullable|string',
            ]);

            $sale = Sale::findOrFail($id);
            $customerIds = User::where('role', 'customer')->pluck('id');
            if (!$customerIds->contains($sale->user_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu sipariş bir müşteri siparişi değil.'
                ], 403);
            }

            $sale->total_price = $request->total_price;
            $sale->pay_type = $request->pay_type;
            if ($request->filled('basket')) {
                $basket = json_decode($request->basket, true);
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($basket)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sepet JSON formatı geçersiz.'
                    ], 422);
                }
                $sale->basket = $request->basket;
            }
            $sale->save();

            Log::info('Other sale updated', [
                'sale_id' => $sale->id,
                'user_id' => Auth::id(),
                'updated_data' => $request->all(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sipariş başarıyla güncellendi.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to update other sale: ' . $e->getMessage(), [
                'sale_id' => $id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Sipariş güncellenemedi: ' . $e->getMessage()
            ], 500);
        }
    }

public function otherReturns(Request $request)
{
    try {
        $returns = RemoteReturn::with(['sale', 'product', 'customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20); 
        Log::info('Returns fetched', [
            'user_id' => Auth::id(),
            'returns_count' => $returns->count(),
            'return_ids' => $returns->pluck('id')->toArray(),
        ]);
        return view('other-returns', compact('returns'));
    } catch (\Exception $e) {
        Log::error('Failed to fetch other returns', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'user_id' => Auth::id(),
        ]);
        return response()->json(['error' => 'İade talepleri yüklenemedi: ' . $e->getMessage()], 500);
    }
}
	
public function processRemoteReturn(Request $request, $id)
{
    $startTime = microtime(true);
    Log::info('Starting processRemoteReturn', [
        'return_id' => $id,
        'user_id' => auth()->id(),
        'request' => $request->all()
    ]);

    try {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        DB::beginTransaction();

        $return = RemoteReturn::with(['sale', 'product', 'customer'])->findOrFail($id);
        
        if ($return->status !== 'pending') {
            throw new \Exception('Bu iade talebi zaten işlenmiş.');
        }

        $return->status = $validated['status'];
        $return->processed_at = now();
        $return->save();

        if ($validated['status'] === 'rejected') {
            DB::commit();
            Log::info('Return rejected', [
                'return_id' => $return->id,
                'sale_id' => $return->sale_id,
                'duration' => microtime(true) - $startTime,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'İade talebi reddedildi.',
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        $sale = $return->sale;
        $product = $return->product;
        
        if (!$sale || !$product) {
            throw new \Exception('Sipariş veya ürün bulunamadı.');
        }

        $paymentDetail = PaymentDetail::where('sale_id', $sale->id)->first();
        if (!$paymentDetail) {
            throw new \Exception('Ödeme detayı bulunamadı.');
        }

        $details = json_decode($paymentDetail->details, true);
        if (!$details || !isset($details['method'])) {
            throw new \Exception('Ödeme detayları geçersiz.');
        }

        $basket = json_decode($sale->basket, true) ?? [];
        $totalSaleQuantity = array_sum(array_column($basket, 'quantity'));
        if ($totalSaleQuantity === 0) {
            throw new \Exception('Sipariş miktarı sıfır, iade hesaplanamadı.');
        }
        $refundAmount = number_format(($return->quantity * $sale->total_price) / $totalSaleQuantity, 2, '.', '');
        $return->return_amount = $refundAmount;
        $return->save();

        $product->stock_quantity += $return->quantity;
        $product->save();

        if ($details['method'] === 'credit_card' && isset($details['payment_id'])) {
            $paymentTransactionId = null;
            $itemTransactions = $details['item_transactions'] ?? [];

            Log::info('Inspecting item_transactions', [
                'return_id' => $return->id,
                'sale_id' => $sale->id,
                'product_id' => $return->product_id,
                'item_transactions' => $itemTransactions,
                'expected_item_id' => 'BI' . $return->product_id,
            ]);

            foreach ($itemTransactions as $transaction) {
                if (isset($transaction['item_id']) && $transaction['item_id'] === 'BI' . $return->product_id) {
                    $paymentTransactionId = $transaction['payment_transaction_id'] ?? null;
                    break;
                }
            }

            if (!$paymentTransactionId) {
                Log::warning('Payment transaction ID not found, marking refund as manual', [
                    'return_id' => $return->id,
                    'sale_id' => $sale->id,
                    'product_id' => $return->product_id,
                    'details' => $details,
                ]);
                $details = array_merge($details, [
                    'refund_status' => 'pending_manual',
                    'refund_amount' => $refundAmount,
                    'refund_date' => now()->toDateTimeString(),
                    'refund_note' => 'Ödeme işlemi ID’si bulunamadı, manuel iade gerekli.',
                ]);
                $paymentDetail->details = json_encode($details);
                $paymentDetail->save();

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'İade talebi onaylandı, ancak ödeme işlemi ID’si bulunamadı. Manuel iade gerekli.',
                    'refund_amount' => $refundAmount,
                ], 200, [], JSON_UNESCAPED_UNICODE);
            }

            $iyzicoResult = $this->iyzicoService->refundPayment(
                $paymentTransactionId,
                $refundAmount,
                'BI' . $return->product_id
            );

            Log::info('Iyzico refund response', [
                'return_id' => $return->id,
                'sale_id' => $sale->id,
                'payment_id' => $details['payment_id'],
                'payment_transaction_id' => $paymentTransactionId,
                'amount' => $refundAmount,
                'item_id' => 'BI' . $return->product_id,
                'iyzico_result' => $iyzicoResult,
                'duration' => microtime(true) - $startTime,
            ]);

            if (!$iyzicoResult['success']) {
                throw new \Exception($iyzicoResult['errorMessage'] ?? 'Iyzico iade işlemi başarısız.');
            }

            $details = array_merge($details, [
                'refund_status' => 'success',
                'refund_amount' => $refundAmount,
                'refund_transaction_id' => $iyzicoResult['refund_transaction_id'] ?? null,
                'refund_date' => now()->toDateTimeString(),
                'refunded_item_id' => $return->product_id,
            ]);
            $paymentDetail->details = json_encode($details);
            $paymentDetail->save();
        } elseif ($sale->pay_type === 3) { // Credit (Veresiye)
            $debt = Debt::where('sale_id', $sale->id)->first();
            if ($debt) {
                $debt->amount -= $refundAmount;
                $debt->description = "İade ID: {$return->id} için borç güncellendi.";
                $debt->save();
                Log::info('Debt updated for credit return', [
                    'debt_id' => $debt->id,
                    'sale_id' => $sale->id,
                    'refund_amount' => $refundAmount,
                ]);
            }
        } elseif ($sale->pay_type === 4) { // Bank transfer
            $details = array_merge($details, [
                'refund_status' => 'pending_manual',
                'refund_amount' => $refundAmount,
                'refund_date' => now()->toDateTimeString(),
            ]);
            $paymentDetail->details = json_encode($details);
            $paymentDetail->save();
        } elseif ($sale->pay_type === 5) { // Cash on delivery
            $details = array_merge($details, [
                'refund_status' => 'pending_manual',
                'refund_amount' => $refundAmount,
                'refund_date' => now()->toDateTimeString(),
            ]);
            $paymentDetail->details = json_encode($details);
            $paymentDetail->save();
        }

        DB::commit();
        Log::info('Return processed successfully', [
            'return_id' => $return->id,
            'sale_id' => $sale->id,
            'status' => $validated['status'],
            'refund_amount' => $refundAmount,
            'duration' => microtime(true) - $startTime,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'İade talebi başarıyla işlendi.',
            'refund_amount' => $refundAmount,
        ], 200, [], JSON_UNESCAPED_UNICODE);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Return processing failed', [
            'return_id' => $id,
            'sale_id' => isset($sale) ? $sale->id : null,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'duration' => microtime(true) - $startTime,
        ]);
        return response()->json([
            'success' => false,
            'message' => 'İade işlemi başarısız: ' . $e->getMessage(),
        ], 500, [], JSON_UNESCAPED_UNICODE);
    }
}
	
public function manageRemoteReturns(Request $request)
{
    try {
        $returns = RemoteReturn::with(['product', 'sale', 'customer'])
            ->orderBy('created_at', 'desc') 
            ->paginate(20);
        Log::info('Returns fetched', [
            'user_id' => Auth::id(),
            'returns_count' => $returns->count(),
            'return_ids' => $returns->pluck('id')->toArray(),
        ]);
        return view('other-returns', compact('returns'));
    } catch (\Exception $e) {
        Log::error('Failed to fetch other returns', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'user_id' => Auth::id(),
        ]);
        return response()->json(['error' => 'İade talepleri yüklenemedi: ' . $e->getMessage()], 500);
    }
}
	
    public function customerReturns(Request $request)
    {
        $customer = Customer::where('email', auth()->user()->email)->first();
        if (!$customer) {
            $returns = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            return view('customer.returns', compact('returns'));
        }
        $returns = RemoteReturn::where('customer_id', $customer->id)
            ->with(['sale', 'product', 'customer'])
            ->paginate(10);
        Log::info('Customer returns fetched', [
            'customer_id' => $customer->id,
            'returns_count' => $returns->count(),
            'return_ids' => $returns->pluck('id')->toArray(),
        ]);
        return view('customer.returns', compact('returns'));
    }


public function refundCallback(Request $request)
{
    $startTime = microtime(true);
    Log::info('Refund callback received', ['request' => $request->all()]);

    try {
        $paymentTransactionId = $request->input('paymentTransactionId');
        $status = $request->input('status');

        if (!$paymentTransactionId || !$status) {
            throw new \Exception('Geçersiz iade geri bildirimi: Eksik parametreler.');
        }

        $paymentDetail = PaymentDetail::whereJsonContains('details->payment_transaction_id', $paymentTransactionId)
            ->orWhereJsonContains('details->refund_transaction_id', $paymentTransactionId)
            ->first();

        if (!$paymentDetail) {
            throw new \Exception('İlgili ödeme detayı bulunamadı.');
        }

        $details = json_decode($paymentDetail->details, true);

        if ($status === 'success') {
            $details['refund_status'] = 'completed';
            $details['refund_verified_at'] = now()->toDateTimeString();
        } else {
            $details['refund_status'] = 'failed';
            $details['refund_error'] = $request->input('errorMessage', 'Bilinmeyen hata');
        }

        $paymentDetail->details = json_encode($details);
        $paymentDetail->save();

        Log::info('Refund callback processed', [
            'payment_transaction_id' => $paymentTransactionId,
            'status' => $status,
            'sale_id' => $paymentDetail->sale_id,
            'duration' => microtime(true) - $startTime,
        ]);

        return response()->json(['success' => true], 200);

    } catch (\Exception $e) {
        Log::error('Refund callback processing failed', [
            'message' => $e->getMessage(),
            'request' => $request->all(),
            'trace' => $e->getTraceAsString(),
            'duration' => microtime(true) - $startTime,
        ]);
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
	private function checkIyzicoCredentials()
{
    if (empty($this->options->getApiKey()) || empty($this->options->getSecretKey())) {
        throw new \Exception('Iyzico API kimlik bilgileri eksik. Lütfen yapılandırmayı kontrol edin.');
    }
}
	
}