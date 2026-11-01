<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;

class ReviewController extends Controller
{
    /**
     * บันทึกรีวิวสินค้าใหม่
     */
    public function store(Request $request, Product $product)
    {
        // 1. ตรวจสอบความถูกต้องของข้อมูล (Rating 1-5 และมี Comment)
        $validated = $request->validate([
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000',
        ]);

        // 2. บันทึกรีวิวลง Database ผูกกับ User ที่ล็อกอินอยู่และสินค้าชิ้นนี้
        $request->user()->reviews()->create([
            'product_id' => $product->id,
            'rating'     => $validated['rating'],
            'comment'    => $validated['comment'],
        ]);

        // 3. คำนวณจำนวนรีวิวและคะแนนเฉลี่ยใหม่ แล้วอัปเดตลงตาราง products
        $product->update([
            'reviews_count'      => $product->reviews()->count(),
            'reviews_avg_rating' => $product->reviews()->avg('rating'),
        ]);

        // 4. Redirect กลับมาหน้าเดิม พร้อม anchor #reviews เพื่อเลื่อนลงมาที่กล่องรีวิวทันที
        return redirect()->to(url()->previous() . '#reviews')
            ->with('success', 'บันทึกรีวิวของคุณเรียบร้อยแล้ว!');
    }
}