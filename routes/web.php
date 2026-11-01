<?php

// use Illuminate\Support\Facades\Route;


// Route::view('/', 'welcome')->name('home');

// Route::middleware(['auth'])->group(function () {
//     Route::view('dashboard', 'dashboard')->name('dashboard');
// });

// require __DIR__.'/settings.php';


use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

/*
| ชั่วคราวฝั่ง Frontend: ใช้ "ข้อมูลสินค้าตัวอย่าง" แทน Database
| เพื่อให้ดูหน้าได้โดยยังไม่ต้องมี Model ของ back
| พอทำ Controller เสร็จ ให้เปลี่ยนเป็นดึงจาก DB จริง
| โดยใช้ "ชื่อ route" และ "ชื่อตัวแปร" เดิม:
|   home           → $products (recommended, newArrivals)
|   products.index → $products (paginate)
|   products.show  → $product, $inWishlist, $related
| ชื่อ field ตรงกับตาราง products ของour team spc na: id, name, description, price, stock, image
| + images (product_images.image_path), reviews_avg_rating, reviews_count, sold (รวม order_items.quantity)
*/

$demoProducts = function () {
    $items = [
        ['Premium Dog Food', 'Complete and balanced dry food for adult dogs.', 590, 50, 4.8, 120, 431],
        ['Cat Scratching Post', 'Durable scratching post for cats.', 450, 30, 4.6, 85, 260],
        ['Pet Feeding Bowl', 'Stainless steel feeding bowl for dogs and cats.', 250, 40, 4.5, 64, 512],
        ['Interactive Pet Toy', 'Interactive toy designed to keep pets active and entertained.', 320, 35, 4.9, 210, 980],
        ['Cozy Pet Bed', 'Soft and washable bed for small to medium pets.', 890, 12, 4.7, 98, 302],
        ['Cat Litter 10L', 'Clumping, low-dust cat litter with odor control.', 199, 80, 4.4, 150, 1240],
        ['Dog Leash & Collar Set', 'Adjustable nylon leash and collar set.', 350, 4, 4.3, 41, 188],
        ['Pet Shampoo', 'Gentle shampoo for sensitive skin.', 180, 60, 4.2, 37, 220],
        ['Bird Cage Large', 'Spacious cage with perches and feeding cups.', 1500, 6, 4.6, 22, 54],
        ['Aquarium Starter Kit', '20L tank with filter and LED light.', 1290, 0, 4.8, 19, 47],
        ['Cat Treats Tuna', 'Healthy tuna treats for cats.', 89, 120, 4.9, 302, 2150],
        ['Hamster Wheel', 'Silent spinner wheel for small pets.', 159, 25, 4.1, 28, 133],
    ];

    return collect($items)->map(fn ($p, $i) => (object) [
        'id' => $i + 1,
        'name' => $p[0],
        'description' => $p[1],
        'price' => $p[2],
        'stock' => $p[3],
        'image' => null,               // ยังไม่มีรูป → โชว์ไอคอนแทน
        'images' => collect(),         // รูปเพิ่ม (product_images)
        'reviews_avg_rating' => $p[4],
        'reviews_count' => $p[5],
        'sold' => $p[6],
        'created_at' => now()->subDays($i),
    ]);
};

Route::get('/', function () use ($demoProducts) {
    $all = $demoProducts();
    $products = $all->sortByDesc('reviews_avg_rating')->values();
   
    return view('home', compact('products'));
})->name('home');

// ชั่วคราวไว้ทดสอบหน้า Products (ค้นหา/เรียง/แบ่งหน้าด้วยข้อมูลตัวอย่าง)
Route::get('/products', function (Request $request) use ($demoProducts) {
    $all = $demoProducts();

    if ($request->filled('search')) {
        $all = $all->filter(fn ($p) => str_contains(strtolower($p->name), strtolower($request->search)));
    }

    $all = match ($request->input('sort')) {
        'newest' => $all->sortByDesc('created_at'),
        'price_asc' => $all->sortBy('price'),
        'price_desc' => $all->sortByDesc('price'),
        default => $all->sortByDesc('reviews_avg_rating'),
    };

    $perPage = 20;
    $page = LengthAwarePaginator::resolveCurrentPage();
    $products = new LengthAwarePaginator(
        $all->values()->forPage($page, $perPage),
        $all->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    return view('products.index', compact('products'));
})->name('products.index');

Route::get('/products/{product}', function ($product) use ($demoProducts) {
    $all = $demoProducts();
    $product = $all->firstWhere('id', (int) $product) ?? abort(404);
    $inWishlist = in_array($product->id, session('demo_wishlist', []));
    $related = $all->where('id', '!=', $product->id)->shuffle()->take(5)->values();

    return view('products.show', compact('product', 'inWishlist', 'related'));
})->name('products.show');

Route::middleware(['auth'])->group(function () {
    // ล็อกอิน/สมัครเสร็จ Fortify จะพามาที่ /dashboard → เด้งต่อไปหน้า Home
    Route::get('dashboard', fn () => redirect()->route('home'))->name('dashboard');
});

//wishlist
// ชั่วคราวไว้ทดสอบหน้า Wishlist: เก็บ id สินค้าที่กดหัวใจไว้ใน session แทน DB
// เขียนทับเป็น Controller ได้ — คง ->name('wishlist.index' / 'wishlist.toggle') และ compact('wishlists') ไว้
Route::middleware(['auth'])->group(function () use ($demoProducts){
Route::get('wishlist', function () use ($demoProducts) {
        $all = $demoProducts()->keyBy('id');
        $wishlists = collect(session('demo_wishlist', []))
            ->filter(fn ($id) => $all->has($id))
             ->map(fn ($id) => $all[$id])
            ->values();

        return view('wishlist', compact('wishlists'));
    })->name('wishlist.index');

Route::post('wishlist/{product}', function ($product) {
        $list = collect(session('demo_wishlist', []));
        $added = ! $list->contains((int) $product);
        $list = $added ? $list->push((int) $product) : $list->reject(fn ($id) => $id === (int) $product);
        session(['demo_wishlist' => $list->values()->all()]);

        return back()->with('status', $added ? __('Added to wish list.') : __('Removed from wish list.'));
    })->name('wishlist.toggle');
});

// Dev Login Route (ใช้เฉพาะช่วงทดสอบระบบ)
Route::get('/dev-login/{id?}', function ($id = 1) {
    // 1. ค้นหา User ตาม ID หรือดึงคนแรกในระบบ
    $user = User::find($id) ?? User::first();

    // 2. ถ้าไม่มี User เลย ให้สร้าง Mock User ขึ้นมาอัตโนมัติ
    if (!$user) {
        $user = User::create([
            'name' => 'Developer Tenten',
            'email' => 'dev@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    // 3. สั่ง Log in เข้าสู่ระบบทันที
    Auth::login($user);

    // 4. พาไปหน้า Home พร้อม Flash Message
    return redirect('/')->with('success', "Logged in as {$user->name} (Dev Mode)");
})->name('dev.login');

// ตะกร้าสินค้า
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// เช็คเอาต์
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');


require __DIR__.'/settings.php';
