<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * บันทึกรีวิวสินค้าใหม่
     * กติกา: ต้องเคยซื้อสินค้านี้ก่อน และ 1 คนรีวิวสินค้าหนึ่งได้ 1 ครั้ง (UNIQUE user_id, product_id)
     */
    public function store(Request $request, Product $product)
    {
        $user = $request->user();
        $backToReviews = url()->previous().'#reviews';

        // 1. ต้องเคยซื้อสินค้านี้ก่อน
        if (! self::hasPurchased($user, $product)) {
            return redirect()->to($backToReviews)
                ->with('error', __('You can only review products you have purchased.'));
        }

        // 2. รีวิวซ้ำไม่ได้
        if ($user->reviews()->where('product_id', $product->id)->exists()) {
            return redirect()->to($backToReviews)
                ->with('error', __('You have already reviewed this product.'));
        }

        // 3. ตรวจสอบความถูกต้องของข้อมูล (Rating 1-5 และมี Comment)
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|max:1000',
        ], [
            'rating.required' => __('Please select a rating from 1 to 5 stars.'),
            'rating.between' => __('Please select a rating from 1 to 5 stars.'),
        ]);

        // 4. บันทึกรีวิว (คะแนนเฉลี่ย/จำนวนรีวิว คำนวณสดตอนแสดงผล ไม่ต้องอัปเดตตาราง products)
        $user->reviews()->create([
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->to($backToReviews)
            ->with('status', __('Thanks! Your review has been posted.'));
    }

    /**
     * ผู้ใช้คนนี้เคยสั่งซื้อสินค้านี้หรือยัง (มีออเดอร์ที่ไม่ถูกยกเลิก และมีสินค้านี้ใน order_items)
     */
    public static function hasPurchased(User $user, Product $product): bool
    {
        return $user->orders()
            ->where('status', '!=', 'cancelled')
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->exists();
    }
}
