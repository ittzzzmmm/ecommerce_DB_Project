<x-layouts::shop :title="__('Wish List')">
    {{-- ตัวแปรจาก backend: $wishlists (แต่ละอันมี id, product) --}}

    <!-- Breadcrumb -->
    <div class="breadcrumbs text-sm text-gray-500 font-[Poppins] p-0 mb-6">
        <ul>
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li>{{ __('Wish List') }}</li>
        </ul>
    </div>

    <div class="flex items-end justify-between gap-4 mb-6">
        <h1 class="text-2xl md:text-4xl font-bold text-[#1F2937]">{{ __('My Wish List') }}</h1>
        @if ($wishlists->isNotEmpty())
            <span class="text-[#5A6BD5]">{{ number_format($wishlists->count()) }} {{ __('items') }}</span>
        @endif
    </div>

    @if ($wishlists->isEmpty())
        <!-- ===== ยังไม่มีสินค้าใน Wish List ===== -->
        <section class="bg-white rounded-xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] px-6 py-16 flex flex-col items-center text-center">
            <div class="w-20 h-20 rounded-full bg-[#EEF0FB] text-[#5A6BD5] grid place-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linejoin="round" d="M12 20s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 10c0 5.65-7 10-7 10Z" />
                </svg>
            </div>
            <h2 class="mt-5 text-xl font-bold text-[#090F3B]">{{ __('Your wish list is empty') }}</h2>
            <p class="mt-1 text-gray-500">{{ __('Tap the heart on any product to save it here.') }}</p>
            <a href="{{ route('products.index') }}" class="btn mt-6 rounded-xl border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white font-[Poppins] font-semibold px-8">
                {{ __('Browse products') }}
            </a>
        </section>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
            @foreach ($wishlists as $wishlist)
                @php($product = $wishlist->product)
                <div class="relative flex flex-col gap-2" data-test="wishlist-item">
                    <x-product-card :product="$product" class="flex-1" />

                    <!-- ปุ่มเอาออกจาก Wish List (หัวใจแดงมุมขวาบน) -->
                    <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}" class="absolute top-2 right-2">
                        @csrf
                        <button type="submit" class="btn btn-circle btn-sm border-none bg-white/90 hover:bg-white shadow text-[#E5484D]"
                                aria-label="{{ __('remove from wishlist') }}" title="{{ __('remove from wishlist') }}" data-test="remove-wishlist-button">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                <path stroke-linejoin="round" d="M12 20s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 10c0 5.65-7 10-7 10Z" />
                            </svg>
                        </button>
                    </form>

                    <!-- ใส่ตะกร้า 1 ชิ้น -->
                     <form method="POST" action="{{ Route::has('cart.add') ? route('cart.add') : '#' }}">
                    @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-sm btn-block rounded-lg border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white font-[Poppins] font-semibold"
                                @disabled($product->stock < 1)>
                            {{ $product->stock < 1 ? __('Sold out') : __('Add to Cart') }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts::shop>
