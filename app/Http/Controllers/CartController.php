<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function add(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);
        $product = Product::findOrFail($productId);

        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'name' => $product->name,
                'price' => $product->sell_price,
                'quantity' => $quantity,
            ];
        }

        // Check stock
        if ($cart[$productId]['quantity'] > $product->stock_quantity) {
            return response()->json(['error' => 'Stok yetersiz.'], 422);
        }

        session()->put('cart', $cart);
        return response()->json(['cart' => $cart]);
    }

    public function update(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');
        $product = Product::findOrFail($productId);

        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                unset($cart[$productId]);
            } else {
                if ($quantity > $product->stock_quantity) {
                    return response()->json(['error' => 'Stok yetersiz.'], 422);
                }
                $cart[$productId]['quantity'] = $quantity;
            }
            session()->put('cart', $cart);
        }

        return response()->json(['cart' => $cart]);
    }
	
	public function placeOrder(Request $request)
{
    $cart = session()->get('cart', []);
    if (empty($cart)) {
        return redirect()->route('customer.customer.shop')->with('error', 'Sepetiniz boş.');
    }

    // Implement order creation logic (e.g., save to database, process payment)
    // Example:
    // $order = Order::create([...]);
    // foreach ($cart as $productId => $item) {
    //     $order->items()->create([...]);
    // }
    // Update stock, etc.

    session()->forget('cart');
    return redirect()->route('customer.customer.shop')->with('success', 'Siparişiniz başarıyla tamamlandı!');
}

    public function remove(Request $request)
    {
        $productId = $request->input('product_id');
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }
        return response()->json(['cart' => $cart]);
    }

    public function index()
    {
        return view('customer.customer_shopping', ['categories' => Category::all()]);
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        return view('customer.customer.checkout', ['cart' => $cart]);
    }
}