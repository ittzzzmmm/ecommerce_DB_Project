<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        return view('checkout.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
        ]);

        // ล้างตะกร้าเมื่อกด Place Order สำเร็จ
        session()->forget('cart');

        return redirect()->route('home')->with('success', 'สั่งซื้อสินค้าเรียบร้อยแล้ว!');
    }
}