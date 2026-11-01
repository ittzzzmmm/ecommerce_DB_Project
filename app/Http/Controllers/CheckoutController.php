<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return to_route('cart.index');
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        foreach ($cart as $id => &$item) {
            if (!is_array($item)) {
                $item = ['quantity' => (int) $item];
            }
            $product = $products->get($id);
            if (!$product || (int) ($item['quantity'] ?? 0) < 1) {
                unset($cart[$id]);
                continue;
            }

            $item['id'] = $product->id;
            $item['name'] = $product->name;
            $item['price'] = (float) $product->price;
            $item['stock'] = (int) $product->stock;
            $item['image'] = $product->image
                ? (str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image))
                : null;
        }
        unset($item);
        session(['cart' => $cart]);

        if (empty($cart)) {
            return to_route('cart.index')->with('error', __('Your cart contains products that are no longer available.'));
        }

        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $shipping = 0;
        $couponCode = session('coupon_code');
        $coupon = $couponCode ? Coupon::where('code', strtoupper($couponCode))->first() : null;
        $discount = $coupon && $this->couponIsAvailable($coupon, auth()->id())
            ? round($subtotal * ((float) $coupon->discount_percent / 100), 2)
            : 0;
        $total = max(0, $subtotal - $discount + $shipping);
        $user = auth()->user();

        return view('checkout.index', compact('cart', 'subtotal', 'discount', 'shipping', 'total', 'user', 'couponCode'));
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'coupon_code' => ['required', 'string', 'max:50'],
        ]);

        $coupon = Coupon::where('code', strtoupper(trim($validated['coupon_code'])))->first();
        if (!$coupon || !$this->couponIsAvailable($coupon, $request->user()->id)) {
            return back()->withInput()->withErrors([
                'coupon_code' => __('This coupon is invalid, expired, or already used.'),
            ]);
        }

        session(['coupon_code' => $coupon->code]);

        return to_route('checkout.index')->with('status', __('Coupon applied.'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'max:2000'],
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return to_route('cart.index')->with('error', __('Your cart is empty.'));
        }

        $order = DB::transaction(function () use ($cart, $validated, $request) {
            $products = Product::whereIn('id', array_keys($cart))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            foreach ($cart as $productId => $item) {
                $product = $products->get($productId);
                $quantity = (int) ($item['quantity'] ?? 0);

                if (!$product || $quantity < 1 || $quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => __('Stock changed while you were shopping. Update your cart and try again.'),
                    ]);
                }

                $subtotal += round((float) $product->price * $quantity, 2);
            }

            $coupon = null;
            $couponCode = session('coupon_code');
            if ($couponCode) {
                $coupon = Coupon::where('code', strtoupper($couponCode))->lockForUpdate()->first();
                if (!$coupon || !$this->couponIsAvailable($coupon, $request->user()->id)) {
                    throw ValidationException::withMessages([
                        'coupon_code' => __('This coupon is no longer available.'),
                    ]);
                }
            }

            $discount = $coupon
                ? round($subtotal * ((float) $coupon->discount_percent / 100), 2)
                : 0;
            $total = max(0, $subtotal - $discount);

            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => $subtotal,
                'discount_amount' => $discount,
                'final_amount' => $total,
                'status' => 'pending',
                'shipping_address' => $validated['address'],
            ]);

            foreach ($cart as $productId => $item) {
                $product = $products->get($productId);
                $quantity = (int) $item['quantity'];
                $unitPrice = (float) $product->price;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => round($unitPrice * $quantity, 2),
                ]);

                $product->decrement('stock', $quantity);
            }

            if ($coupon) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => $request->user()->id,
                    'order_id' => $order->id,
                    'used_at' => now(),
                ]);
                $coupon->increment('used_count');
            }

            return $order;
        });

        session()->forget(['cart', 'coupon_code']);

        return to_route('home')->with('status', __('Order #:number placed successfully.', ['number' => $order->id]));
    }

    private function couponIsAvailable(Coupon $coupon, int $userId): bool
    {
        $now = now();

        return $coupon->is_active
            && $coupon->start_at->lte($now)
            && $coupon->expire_at->gte($now)
            && ($coupon->usage_limit === null || $coupon->used_count < $coupon->usage_limit)
            && !CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $userId)->exists();
    }
}
