<x-layouts::shop :title="$product->name">
@php
        $toUrl = fn ($path) => str_starts_with($path, 'http') ? $path : asset('storage/'.$path);

        // รูปหลัก (products.image) + รูปเพิ่ม (product_images.image_path)
        $extraImages = optional($product->images)->pluck('image_path') ?? collect();

        $images = collect([$product->image])
            ->merge($extraImages)
            ->filter()
            ->map($toUrl)
            ->values();

        $rating = $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : null;
        $inStock = $product->stock > 0;

        // route ของ backend (ถ้ายังไม่มี ปุ่มจะยังกดไม่ได้ แต่หน้าไม่พัง)
        $canAddToCart = Route::has('cart.add');
        $canWishlist = Route::has('wishlist.toggle');
    @endphp

    <!-- Breadcrumb -->
    <div class="breadcrumbs text-sm text-gray-500 font-[Poppins] p-0 mb-6">
        <ul>
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li><a href="{{ route('products.index') }}">{{ __('Products') }}</a></li>
            <li class="truncate max-w-48 md:max-w-none">{{ $product->name }}</li>
        </ul>
    </div>

    <!-- ===== การ์ดรายละเอียดสินค้า (ตาม Figma: เงาม่วงด้านล่าง) ===== -->
    <section class="bg-white rounded-xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-5 md:p-10 grid md:grid-cols-[minmax(0,420px)_1fr] gap-8 md:gap-12">

        <!-- รูปสินค้า + รูปย่อย -->
        <div>
            <div class="relative aspect-square border border-gray-800 rounded-sm bg-white grid place-items-center overflow-hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" /><path d="m21 15-5-5L5 21" />
                </svg>
                @if ($images->isNotEmpty())
                    <img id="main-image" src="{{ $images->first() }}" alt="{{ $product->name }}" onerror="this.remove()"
                         class="absolute inset-0 w-full h-full object-cover">
                @endif
            </div>

            @if ($images->count() > 1)
                <div class="mt-3 grid grid-cols-5 gap-2">
                    @foreach ($images as $i => $src)
                        <button type="button" onclick="document.getElementById('main-image') && (document.getElementById('main-image').src = '{{ $src }}')"
                                class="relative aspect-square border rounded-sm overflow-hidden bg-[#F4F5FB] hover:border-primary hover:ring-2 hover:ring-primary/30 transition">
                            <img src="{{ $src }}" alt="" onerror="this.remove()" class="absolute inset-0 w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- ข้อมูลสินค้า -->
        <div class="flex flex-col">
            <h1 class="text-2xl md:text-4xl font-bold leading-tight">{{ $product->name }}</h1>

            <!-- คะแนน + ขายแล้ว -->
            <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-1 text-[#5A6BD5]">
                <span class="inline-flex items-center gap-1">
                    @for ($s = 1; $s <= 5; $s++)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $rating && $s <= round($product->reviews_avg_rating) ? 'text-[#F5C518]' : 'text-gray-300' }}" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.5l2.9 6.1 6.6.8-4.9 4.6 1.3 6.6L12 17.3l-5.9 3.3 1.3-6.6L2.5 9.4l6.6-.8z" />
                        </svg>
                    @endfor
                    <span class="ml-1 font-medium text-black">{{ $rating ?? '-' }}</span>
                </span>
                <a href="#reviews" class="hover:underline">{{ number_format($product->reviews_count) }} {{ __('rating') }}</a>
                <span>{{ number_format((int) $product->sold) }} {{ __('sold') }}</span>
            </div>

            <!-- ราคา -->
            <p class="mt-5 text-3xl md:text-4xl font-bold text-[#E5484D]">{{ number_format($product->price) }} {{ __('Baht') }}</p>

            <!-- คำอธิบาย -->
            @if ($product->description)
                <p class="mt-5 text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
            @endif

            <div class="my-6 border-t border-gray-100"></div>

            <!-- ฟอร์ม Add to Cart: ส่ง product_id + quantity ให้ backend -->
            <form method="POST" action="{{ $canAddToCart ? route('cart.add') : '#' }}" class="flex flex-col gap-6">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <!-- Quantity  <  1  >   IN STOCK -->
                <div class="flex flex-wrap items-center gap-4 font-[Roboto]">
                    <span class="text-[#8C96D9]">{{ __('Quantity') }}</span>
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="stepQty(-1)" class="btn btn-sm btn-ghost btn-square text-[#5A6BD5] text-lg" aria-label="{{ __('Decrease') }}" @disabled(! $inStock)>&lt;</button>
                        <input id="quantity" type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="{{ max(1, $product->stock) }}"
                               class="w-14 h-10 text-center rounded-lg border border-[#B9C2FF] text-[#5A6BD5] focus:outline-none focus:ring-2 focus:ring-primary [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                               @disabled(! $inStock)>
                        <button type="button" onclick="stepQty(1)" class="btn btn-sm btn-ghost btn-square text-[#5A6BD5] text-lg" aria-label="{{ __('Increase') }}" @disabled(! $inStock)>&gt;</button>
                    </div>
                    @if ($inStock)
                        <span class="text-sm uppercase tracking-wide text-[#8C96D9]">{{ __('In stock') }} <span class="normal-case">({{ number_format($product->stock) }} {{ __('available') }})</span></span>
                    @else
                        <span class="text-sm uppercase tracking-wide font-semibold text-[#E5484D]">{{ __('Out of stock') }}</span>
                    @endif
                </div>
                @error('quantity')
                    <p class="text-error text-sm -mt-4">{{ $message }}</p>
                @enderror

                <!-- ปุ่ม Add to Cart -->
                <div class="flex flex-wrap items-center gap-3">
                    @auth
                        <button type="submit" class="btn btn-lg rounded-xl border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white font-[Poppins] font-semibold px-10"
                                data-test="add-to-cart-button" @disabled(! $inStock || ! $canAddToCart)>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.4 10.2a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.76L20 8H6.2" />
                                <circle cx="9" cy="19" r="1.5" /><circle cx="17" cy="19" r="1.5" />
                            </svg>
                            {{ __('Add to Cart') }}
                        </button>
                    @else
                        <!-- ต้องล็อกอินก่อนสั่งซื้อ -->
                        <a href="{{ route('login') }}" class="btn btn-lg rounded-xl border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white font-[Poppins] font-semibold px-10">
                            {{ __('Add to Cart') }}
                        </a>
                    @endauth
                </div>
            </form>

            <!-- add to wishlist -->
            <div class="mt-5">
                @auth
                    <form method="POST" action="{{ $canWishlist ? route('wishlist.toggle', $product->id) : '#' }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 text-[#5A6BD5] hover:text-[#3B4BB0] disabled:opacity-50" data-test="wishlist-button" @disabled(! $canWishlist)>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="{{ $inWishlist ? 'currentColor' : 'none' }}">
                                <path stroke-linejoin="round" d="M12 20s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 10c0 5.65-7 10-7 10Z" />
                            </svg>
                            {{ $inWishlist ? __('remove from wishlist') : __('add to wishlist') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-[#5A6BD5] hover:text-[#3B4BB0]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linejoin="round" d="M12 20s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 10c0 5.65-7 10-7 10Z" />
                        </svg>
                        {{ __('add to wishlist') }}
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- ===== Reviews & Rating (component ของตำแหน่งที่ 4: components/reviews.blade.php) ===== -->
    <section id="reviews" class="mt-10 scroll-mt-28">
        @if (View::exists('components.reviews'))
            @include('components.reviews', ['product' => $product])
        @else
            <div class="bg-white rounded-xl shadow-sm p-6 md:p-8">
                <h2 class="text-xl md:text-2xl font-bold text-[#090F3B]">{{ __('Reviews & Rating') }}</h2>
                <p class="text-gray-500 mt-2">{{ __('Reviews section coming soon.') }}</p>
            </div>
        @endif
    </section>

    <!-- ===== สินค้าอื่นที่น่าสนใจ ===== -->
    @if (isset($related) && $related->isNotEmpty())
        <section class="mt-12">
            <h2 class="text-2xl font-bold text-[#1F2937] mb-6">{{ __('You may also like') }}</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
                @foreach ($related as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- ปุ่ม < > ของ Quantity (ไม่ให้ต่ำกว่า 1 หรือเกิน stock) -->
    <script>
        function stepQty(step) {
            const input = document.getElementById('quantity');
            const max = parseInt(input.max) || 1;
            const next = (parseInt(input.value) || 1) + step;
            input.value = Math.min(max, Math.max(1, next));
        }
    </script>
</x-layouts::shop>
