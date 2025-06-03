<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        $categories = Category::all();
        return view('urunyonetimi', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('urunekle', compact('categories'));
    }

public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sell_price' => 'required|numeric|min:0',
            'buy_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'barcode' => 'nullable|string|max:255',
            'entry_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'active' => 'required|boolean',
            'description' => 'nullable|string',
        ]);

        $product = new Product();
        $product->name = $validated['name'];
        $product->sell_price = $validated['sell_price'];
        $product->buy_price = $validated['buy_price'];
        $product->stock_quantity = $validated['stock_quantity'];
        $product->category_id = $validated['category_id'];
        $product->barcode = $validated['barcode'] ?? null;
        $product->entry_date = $validated['entry_date'] ?? null;
        $product->expiry_date = $validated['expiry_date'] ?? null;
        $product->active = $validated['active'];
        $product->description = $validated['description'] ?? null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Ürün başarıyla eklendi.',
            'product' => $product,
            'image_url' => $product->image ? asset('storage/' . $product->image) : null
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Failed to create product', ['error' => $e->getMessage()]);
        
        return response()->json([
            'success' => false,
            'message' => 'Ürün eklenirken bir hata oluştu: ' . $e->getMessage(),
            'errors' => $e->getMessage()
        ], 500);
    }
}
       
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $imagePath = $product->image ? 'storage/' . $product->image : null;
        $imageExists = $imagePath && Storage::disk('public')->exists($product->image);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'category_id' => $product->category_id,
            'category' => $product->category,
            'barcode' => $product->barcode,
            'buy_price' => $product->buy_price,
            'sell_price' => $product->sell_price,
            'stock_quantity' => $product->stock_quantity,
            'entry_date' => $product->entry_date ? $product->entry_date->format('Y-m-d') : null,
            'expiry_date' => $product->expiry_date ? $product->expiry_date->format('Y-m-d') : null,
            'active' => $product->active,
            'description' => $product->description,
            'image' => $imageExists ? $product->image : null,
            'image_url' => $imageExists ? asset('storage/' . $product->image) : null,
        ]);
    }

  public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $request->validate([
        'name' => 'required|string|max:255',
        'category_id' => 'required|integer|exists:categories,id',
        'barcode' => 'nullable|string|max:255',
        'buy_price' => 'required|numeric|min:0',
        'sell_price' => 'required|numeric|min:0',
        'stock_quantity' => 'required|integer|min:0',
        'entry_date' => 'nullable|date',
        'expiry_date' => 'nullable|date',
        'active' => 'required|boolean',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'description' => 'nullable|string',
    ]);

    $data = $request->all();
    if ($request->hasFile('image')) {
        try {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $image = $request->file('image');
            $path = $image->store('products', 'public');
            $fullPath = storage_path('app/public/' . $path);
            \Log::info('Image update attempt', [
                'original_name' => $image->getClientOriginalName(),
                'stored_path' => $path,
                'full_path' => $fullPath,
                'exists' => file_exists($fullPath),
                'permissions' => substr(sprintf('%o', fileperms($fullPath)), -4)
            ]);
            $data['image'] = $path;
        } catch (\Exception $e) {
            \Log::error('Image update failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Image update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    $product->update($data);
    return response()->json([
        'success' => true,
        'message' => 'Ürün güncellendi.',
        'product' => $product,
        'image_url' => $product->image ? asset('storage/' . $product->image) : null
    ]);
}

    public function destroy($id)
    {
        try {
            $urun = Product::findOrFail($id);
            $result = $urun->delete();

            \Log::info('Delete result', ['result' => $result]);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ürün başarıyla silindi.'
                ]);
            }

            return redirect()->route('urunyonetimi')->with('success', 'Ürün başarıyla silindi.');
        } catch (\Exception $e) {
            \Log::error('Error deleting product', ['error' => $e->getMessage()]);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ürün silinemedi: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('urunyonetimi')->with('error', 'Ürün silinemedi: ' . $e->getMessage());
        }
    }

    public function getById(Request $request)
    {
        $product = Product::find($request->id);
        if (!$product) {
            return response()->json(['error' => 'Ürün bulunamadı.'], 404);
        }
        return response()->json($product);
    }

    public function getByBarcode(Request $request)
    {
        $product = Product::where('barcode', $request->barcode)->first();
        if (!$product) {
            return response()->json(['error' => 'Ürün bulunamadı.'], 404);
        }
        return response()->json($product);
    }
    
public function getByCategory(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'category_id' => 'required|integer|exists:categories,id'
            ]);

            // Fetch the category
            $category = Category::findOrFail($validated['category_id']);
            $categoryName = trim($category->name);

            Log::info('Fetching products for category', [
                'category_id' => $validated['category_id'],
                'category_name' => $categoryName
            ]);

            // Fetch products for the given category
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

            Log::info('Products retrieved', [
                'category_id' => $validated['category_id'],
                'products_count' => $products->count(),
                'products' => $products->toArray()
            ]);

            return response()->json($products, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            Log::error('Error fetching products by category: ' . $e->getMessage(), [
                'category_id' => $request->input('category_id'),
                'exception' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Ürünler yüklenemedi: ' . $e->getMessage()
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
	
}