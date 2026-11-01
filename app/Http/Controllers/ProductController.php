<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // หน้าแรก (Home)
    public function home()
    {
        // ดึงจาก DB จริง เรียงตามคะแนนรีวิว
        $products = Product::orderByDesc('reviews_avg_rating')->get();

        return view('home', compact('products'));
    }

    // หน้ารวมสินค้า ค้นหา และแบ่งหน้า (Products Index)
    public function index(Request $request)
    {
        $query = Product::query();

        // ค้นหาตามชื่อสินค้า
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // จัดเรียงลำดับ
        match ($request->input('sort')) {
            'newest'     => $query->latest(),
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default      => $query->orderByDesc('reviews_avg_rating'),
        };

        $products = $query->paginate(20)->withQueryString();

        return view('products.index', compact('products'));
    }

    // หน้ารายละเอียดสินค้า (Product Detail)
    public function show(Product $product)
    {
        // โหลดรีวิวพร้อมผู้เขียน
        $product->load(['reviews.user']);

        // ตรวจสอบสถานะ Wishlist
        $inWishlist = in_array($product->id, session('demo_wishlist', []));

        // สินค้าแนะนำเพิ่มเติม 5 ชิ้น
        $related = Product::where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(5)
            ->get();

        return view('products.show', compact('product', 'inWishlist', 'related'));
    }
}