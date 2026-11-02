<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::withAvg('reviews', 'rating')
            ->withSum('orderItems as sold', 'quantity')
            ->orderByDesc('reviews_avg_rating')
            ->get();

        return view('dashboard', compact('products'));
    }
}
