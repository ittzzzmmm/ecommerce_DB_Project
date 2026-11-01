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
        $products = Product::withStats()->orderByDesc('reviews_avg_rating')->get();

        return view('home', compact('products'));
    }

    // หน้ารวมสินค้า ค้นหา และแบ่งหน้า (Products Index)
    public function index(Request $request)
    {
        $query = Product::withStats();

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
        // โหลดรีวิวพร้อมผู้เขียน (ดึงแค่ id, name ของ user) + คะแนนเฉลี่ย/จำนวนรีวิว/ยอดขายแบบคำนวณสด
        $product->load(['reviews.user:id,name'])->loadStats();

        // สิทธิ์รีวิว: ต้องเคยสั่งซื้อสินค้านี้ และยังไม่เคยรีวิวสินค้านี้ (1 คน 1 รีวิว ต่อสินค้า)
        $user = auth()->user();
        $hasPurchased = $user ? ReviewController::hasPurchased($user, $product) : false;
        $hasReviewed = $user ? $product->reviews->contains('user_id', $user->id) : false;

        // ตรวจสอบสถานะ Wishlist
        $inWishlist = in_array($product->id, session('demo_wishlist', []));

        // สินค้าแนะนำเพิ่มเติม 5 ชิ้น
        $related = Product::withStats()->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(5)
            ->get();

        return view('products.show', compact('product', 'inWishlist', 'related', 'hasPurchased', 'hasReviewed'));
    }
}