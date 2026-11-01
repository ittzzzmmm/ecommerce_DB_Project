<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Wish List เก็บในตาราง wishlists (user_id, product_id, UNIQUE คู่นี้) ตาม ER
 * ⇒ ผูกกับบัญชี ไม่ใช่ browser: บัญชีใหม่เริ่มว่างเสมอ และ login เครื่องไหนก็เห็นรายการเดิม
 */
class WishlistController extends Controller
{
    public function index(Request $request)
    {
        // ใหม่สุดขึ้นก่อน
        $productIds = $request->user()->wishlists()->latest('id')->pluck('product_id');

        $wishlists = Product::withStats()
            ->whereIn('id', $productIds)
            ->get()
            ->sortBy(fn (Product $product) => $productIds->search($product->id))
            ->values();

        return view('wishlist', compact('wishlists'));
    }

    public function toggle(Request $request, Product $product)
    {
        $existing = $request->user()->wishlists()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $request->user()->wishlists()->create(['product_id' => $product->id]);
        }

        return back()->with('status', $existing ? __('Removed from wish list.') : __('Added to wish list.'));
    }
}
