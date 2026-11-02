<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index(): View
    {
        $cart = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        foreach ($cart as $id => &$item) {
            if (! is_array($item)) {
                $item = ['quantity' => (int) $item];
            }
            $product = $products->get((int) $id);
            if (! $product || (int) ($item['quantity'] ?? 0) < 1) {
                unset($cart[$id]);

                continue;
            }

            $item['id'] = $product->id;
            $item['name'] = $product->name;
            $item['price'] = (float) $product->price;
            $item['stock'] = (int) $product->stock;
            $item['image'] = $product->image
                ? (str_starts_with($product->image, 'http') ? $product->image : asset('storage/'.$product->image))
                : null;
        }
        unset($item);

        session(['cart' => $cart]);

        $itemCount = 0;
        $subtotal = 0;
        foreach ($cart as $item) {
            $itemCount += (int) ($item['quantity'] ?? 0);
            $subtotal += (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0);
        }
        $shipping = 0;
        $total = $subtotal + $shipping;

        return view('cart.index', compact('cart', 'itemCount', 'subtotal', 'shipping', 'total'));
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::query()->whereKey($validated['product_id'])->firstOrFail();
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
            ? (str_starts_with($product->image, 'http') ? $product->image : asset('storage/'.$product->image))
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

    public function update(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session('cart', []);
        if (! isset($cart[$product->id])) {
            return $request->expectsJson()
                ? response()->json(['message' => __('This item is no longer in your cart.')], 404)
                : to_route('cart.index');
        }

        if ($validated['quantity'] > $product->stock) {
            $message = __('Only :stock units are currently available.', ['stock' => $product->stock]);

            return $request->expectsJson()
                ? response()->json(['message' => $message], 422)
                : back()->with('error', $message);
        }

        $cart[$product->id]['quantity'] = $validated['quantity'];
        session(['cart' => $cart]);

        // ปุ่ม + / − ในหน้า Cart เรียกแบบ fetch → ส่งตัวเลขใหม่กลับไปอัปเดตหน้าโดยไม่รีโหลด
        if ($request->expectsJson()) {
            return response()->json($this->summary($cart, $product));
        }

        return to_route('cart.index')->with('status', __('Cart updated.'));
    }

    public function remove(Product $product): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return to_route('cart.index')->with('status', __('Item removed from cart.'));
    }

    /**
     * ตัวเลขสรุปของตะกร้า (ใช้ตอบกลับปุ่ม + / −)
     */
    /** @param array<int|string, array<string, int|float|string|null>> $cart
     * @return array<string, int|float|string>
     */
    private function summary(array $cart, Product $product): array
    {
        $item = $cart[$product->id];
        $quantity = (int) $item['quantity'];
        $price = (float) ($item['price'] ?? $product->price);
        $itemCount = collect($cart)->sum(fn ($row) => (int) ($row['quantity'] ?? 0));
        $subtotal = collect($cart)->sum(fn ($row) => (float) ($row['price'] ?? 0) * (int) ($row['quantity'] ?? 0));

        return [
            'quantity' => $quantity,
            'stock' => (int) $product->stock,
            'line_total' => number_format($price * $quantity, 2),
            'item_count' => $itemCount,
            'item_label' => $itemCount.' '.Str::plural('item', $itemCount),
            'subtotal' => number_format($subtotal, 2),
            'total' => number_format($subtotal, 2),
            'message' => __('Cart updated.'),
        ];
    }
}
