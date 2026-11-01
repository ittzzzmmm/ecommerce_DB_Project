<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// --- หน้าหลักและแสดงสินค้า ---
Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// --- ระบบที่ต้องเข้าสู่ระบบ (Authenticated Only) ---
Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', fn() => redirect()->route('home'))->name('dashboard');

    // Wishlist
    Route::get('wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // รีวิวสินค้า
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // เช็คเอาต์ (ต้องล็อกอินก่อนสั่งซื้อ)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');

    // ประวัติการสั่งซื้อ (My Purchase)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// --- ตะกร้าสินค้า (Cart) ---
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');


// --- Dev Login (เฉพาะช่วงทดสอบ) ---
Route::get('/dev-login/{id?}', function ($id = 1) {
    $user = User::find($id) ?? User::first();

    if (!$user) {
        $user = User::create([
            'name' => 'Developer',
            'email' => 'dev@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    Auth::login($user);

    return redirect('/')->with('success', "Logged in as {$user->name} (Dev Mode)");
})->name('dev.login');

require __DIR__ . '/settings.php';
