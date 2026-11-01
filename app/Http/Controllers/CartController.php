<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        foreach ($cart as $id => &$item) {
            if (!is_array($item)) {
                $item = ['quantity' => (int) $item];
            }
            $product = $products->get($id);
            if (!$product || (int) ($item['quantity'] ?? 0) < 1) {
                unset($cart[$id]);
                continue;
            }

            $item['id'] = $product->id;
            $item['name'] = $product->name;
            $item['price'] = (float) $product->price;
            $item['stock'] = (int) $product->stock;
            $item['image'] = $product->image
                ? (str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image))
                : null;
        }
        unset($item);

        session(['cart' => $cart]);

        $itemCount = collect($cart)->sum('quantity');
        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $shipping = 0;
        $total = $subtotal + $shipping;

        return view('cart.index', compact('cart', 'itemCount', 'subtotal', 'shipping', 'total'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $cart = session('cart', []);
        $id = $product->id;
        $existingItem = $cart[$id] ?? [];
        $existingQuantity = is_array($existingItem)
            ? (int) ($existingItem['quantity'] ?? 0)
            : (int) $existingItem;
        $newQuantity = $existingQuantity + $validated['quantity'];

        if ($newQuantity > $product->stock) {
            return back()->withInput()->with('error', __('Only :stock units are currently available.', ['stock' => $product->stock]));
        }

        $imageUrl = $product->image
            ? (str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image))
            : null;

        $cart[$id] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'image' => $imageUrl,
            'quantity' => $newQuantity,
        ];

        session(['cart' => $cart]);

        return to_route('cart.index')->with('status', __('Added to cart. Stock and sold totals update after checkout.'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session('cart', []);
        if (!isset($cart[$product->id])) {
            return to_route('cart.index');
        }

        if ($validated['quantity'] > $product->stock) {
            return back()->with('error', __('Only :stock units are currently available.', ['stock' => $product->stock]));
        }

        $cart[$product->id]['quantity'] = $validated['quantity'];
        session(['cart' => $cart]);

        return to_route('cart.index')->with('status', __('Cart updated.'));
    }

    public function remove(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return to_route('cart.index')->with('status', __('Item removed from cart.'));
    }
}
