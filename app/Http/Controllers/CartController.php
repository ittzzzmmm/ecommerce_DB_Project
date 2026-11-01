<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function index(){
        $cart = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');
        $items = collect($cart)->map(function ($quantity, $id) use ($products) {
            if (!$products->has($id)) {
                return null;
            }
            $product = $products[$id];
            return [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $product->price * $quantity,
            ];
        })->filter()->values();
        $subtotal = $items->sum('subtotal');
        return view('cart.index', compact('items', 'subtotal'));

    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $cart = session('cart', []);

        $quantity = $validated['quantity'];

        $newQuantity = ($cart[$product->id] ?? 0) + $quantity;

        if ($newQuantity > $product->stock) {
            return back()->with('error', 'Insufficient stock');
        }

        $cart[$product->id] = $newQuantity;

        session(['cart' => $cart]);

        return back()->with('status', __('Added to cart!'));
    }

    public function update(Request $request, Product $product){
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $validated['quantity'];

        if ($quantity > $product->stock) {
            return back()->with('error', 'สินค้าในสต็อกไม่เพียงพอ');
        }

        $cart = session('cart', []);

        if (!array_key_exists($product->id, $cart)) {
            return back();
        }

        $cart[$product->id] = $quantity;

        session(['cart' => $cart]);

        return back()->with('status', __('Cart updated.'));
    }

    public function remove(Product $product){
        $cart = session('cart', []);

        unset($cart[$product->id]);

        session(['cart' => $cart]);

        return back();
    }
}
