<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $products = Product::withAvg('reviews', 'rating')
            ->withSum('orderItems as sold', 'quantity')
            ->orderByDesc('reviews_avg_rating')
            ->get();

        return view('dashboard', compact('products'));
    }
}
