<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    public function index(){
        $products = Product::withAvg('reviews', 'rating')
                            ->orderByDesc('reviews_avg_rating')
                            ->get();
                            
        return view('dashboard', compact('products'));
    }
}
