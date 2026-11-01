<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * ประวัติการสั่งซื้อของผู้ใช้ (My Purchase)
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('items.product:id,name,image')
            ->withSum('items as item_count', 'quantity')
            ->latest('id')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * รายละเอียดคำสั่งซื้อ + ปุ่มไปรีวิวสินค้าแต่ละชิ้น
     */
    public function show(Request $request, Order $order)
    {
        // ดูได้เฉพาะออเดอร์ของตัวเอง
        abort_unless((int) $order->user_id === (int) $request->user()->id, 404);

        $order->load(['items.product:id,name,image', 'couponUsage.coupon:id,code,discount_percent']);

        // สินค้าในออเดอร์นี้ที่ผู้ใช้รีวิวไปแล้ว (1 คน 1 รีวิวต่อสินค้า)
        $reviewedProductIds = $request->user()->reviews()
            ->whereIn('product_id', $order->items->pluck('product_id'))
            ->pluck('product_id')
            ->all();

        return view('orders.show', compact('order', 'reviewedProductIds'));
    }
}
