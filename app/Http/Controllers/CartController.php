<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // แสดงหน้าตะกร้าสินค้า
    public function index()
    {
        $cart = session()->get('cart', []);
        
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = 0;
        $total = $subtotal + $shipping;

        return view('cart.index', compact('cart', 'subtotal', 'shipping', 'total'));
    }

    // เพิ่มสินค้าลงตะกร้า (จากปุ่ม Add to Cart ของเพื่อน)
    public function add(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity'   => 'required|integer|min:1',
    ]);

    // ตัด with('images') ออก เปลี่ยนเป็น findOrFail ตรงๆ
    $product = Product::findOrFail($request->product_id);

    // ดึง URL รูปภาพ
    $imageUrl = $product->image 
        ? (str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image))
        : null;

    $cart = session()->get('cart', []);
    $id = $product->id;

    if (isset($cart[$id])) {
        $cart[$id]['quantity'] += (int) $request->quantity;
    } else {
        $cart[$id] = [
            'id'       => $product->id,
            'name'     => $product->name,
            'price'    => (float) $product->price,
            'image'    => $imageUrl,
            'quantity' => (int) $request->quantity,
        ];
    }

    session()->put('cart', $cart);

    return redirect()->route('cart.index')->with('success', 'เพิ่มสินค้าแล้ว');
}

    // ปรับจำนวน (+ / -) ในหน้า cart
    public function update(Request $request, $id)
    {
        $quantity = (int) $request->input('quantity', 1);
        $cart = session()->get('cart', []);

        if (isset($cart[$id]) && $quantity > 0) {
            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }

        return back();
    }

    // ลบสินค้าออกจากตะกร้า
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'ลบสินค้าออกจากตะกร้าแล้ว');
    }
}