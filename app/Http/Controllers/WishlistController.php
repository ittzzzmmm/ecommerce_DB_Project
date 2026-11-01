<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistIds = session('demo_wishlist', []);
        $wishlists = Product::whereIn('id', $wishlistIds)->get();

        return view('wishlist', compact('wishlists'));
    }

    public function toggle($product)
    {
        $productId = (int) $product;
        $list = collect(session('demo_wishlist', []));
        $added = ! $list->contains($productId);

        $list = $added 
            ? $list->push($productId) 
            : $list->reject(fn ($id) => $id === $productId);

        session(['demo_wishlist' => $list->values()->all()]);

        return back()->with('status', $added ? __('Added to wish list.') : __('Removed from wish list.'));
    }
}
