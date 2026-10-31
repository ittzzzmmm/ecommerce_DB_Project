<x-layouts::shop :title="__('Home')">
    <div class="flex flex-col gap-12 md:gap-16">

        <!-- ===== แบนเนอร์แนะนำร้านค้า ===== -->
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#090F3B] via-[#1E2A6E] to-[#5A6BD5] text-white shadow-xl">
            <!-- วงกลมตกแต่ง -->
            <div class="pointer-events-none absolute -top-24 -right-24 w-80 h-80 rounded-full bg-white/10"></div>
            <div class="pointer-events-none absolute -bottom-32 right-40 w-72 h-72 rounded-full bg-[#5A6BD5]/40 blur-2xl"></div>

            <div class="relative grid md:grid-cols-2 gap-8 items-center px-6 py-10 md:px-14 md:py-14">
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-white/15 text-xs font-[Poppins] tracking-wide">
                        {{ __('Welcome to CPE10 Shop') }}
                    </span>
                    <h1 class="mt-4 text-3xl md:text-5xl font-bold leading-tight">
                        {{ __('Everything you love,') }}<br>
                        <span class="text-[#B9C2FF]">{{ __('in one place.') }}</span>
                    </h1>
                    <p class="mt-4 text-white/80 max-w-md">
                        {{ __('Browse our products, save favorites to your wish list, read real reviews and get extra savings with discount coupons.') }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3 font-[Poppins]">
                        <a href="{{ route('products.index') }}" class="btn rounded-full border-none bg-white text-[#090F3B] hover:bg-[#EEF0FB] px-7 font-semibold">
                            {{ __('Shop now') }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
                        </a>
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-outline rounded-full border-white/60 text-white hover:bg-white/10 hover:border-white px-7 font-semibold">
                                {{ __('Create an account') }}
                            </a>
                        @endguest
                    </div>
                </div>

                <!-- สินค้าเด่น 1 ชิ้น -->
                @if ($featured = $products->first())
                    <div class="hidden md:block justify-self-end w-64 rotate-2 hover:rotate-0 transition duration-300">
                        <x-product-card :product="$featured" class="shadow-2xl" />
                        <p class="mt-3 text-center text-sm text-white/80 font-[Poppins]">{{ __('Top rated') }} ★</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- ===== จุดเด่นของร้าน (ฟีเจอร์พิเศษของโปรเจกต์) ===== -->
        <section class="grid sm:grid-cols-3 gap-4">
            @foreach ([
                ['title' => __('Wish List'), 'text' => __('Tap the heart to save products for later.'), 'icon' => 'M12 20s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 10c0 5.65-7 10-7 10Z'],
                ['title' => __('Reviews & Rating'), 'text' => __('Rate products 1–5 stars and share your thoughts.'), 'icon' => 'M12 2.5l2.9 6.1 6.6.8-4.9 4.6 1.3 6.6L12 17.3l-5.9 3.3 1.3-6.6L2.5 9.4l6.6-.8z'],
                ['title' => __('% Discount Coupon'), 'text' => __('Enter your coupon code at checkout to save.'), 'icon' => 'M9 15l6-6m-5.5.5h.01m4.99 5h.01M4 7a3 3 0 0 0 3-3h10a3 3 0 0 0 3 3v10a3 3 0 0 0-3 3H7a3 3 0 0 0-3-3z'],
            ] as $feature)
                <div class="flex items-start gap-4 bg-white rounded-2xl p-5 shadow-sm border-b-4 border-primary/70">
                    <div class="shrink-0 w-12 h-12 rounded-full bg-[#090F3B] text-white grid place-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feature['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-[#090F3B]">{{ $feature['title'] }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $feature['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </section>

        <!-- ===== Recommended Products (ตาม Figma) ===== -->
        <section>
            <div class="flex items-end justify-between gap-4 mb-6">
                <h2 class="text-2xl md:text-4xl font-bold text-[#1F2937]">{{ __('Recommended Products') }}</h2>
                <a href="{{ route('products.index') }}" class="link link-primary link-hover font-[Poppins] text-sm shrink-0">{{ __('View all') }} &rarr;</a>
            </div>

            @if ($products->isEmpty())
                <div class="bg-white rounded-2xl p-10 text-center text-gray-500">{{ __('No products yet.') }}</div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
                    @foreach ($products->take(10) as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </section>

        <!-- ===== สินค้าใหม่ ===== -->
       @php($newArrivals = $products->sortByDesc('created_at')->take(5))
        @if ($newArrivals->isNotEmpty())
            <section>
                <div class="flex items-end justify-between gap-4 mb-6">
                    <h2 class="text-2xl md:text-3xl font-bold text-[#1F2937]">{{ __('New Arrivals') }}</h2>
                    <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="link link-primary link-hover font-[Poppins] text-sm shrink-0">{{ __('View all') }} &rarr;</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
                    @foreach ($newArrivals as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        <!-- ===== แบนเนอร์คูปอง ===== -->
        <section class="rounded-3xl bg-white shadow-sm border-2 border-dashed border-primary/50 px-6 py-8 md:px-12 flex flex-col md:flex-row items-center gap-6 justify-between">
            <div class="flex items-center gap-5">
                <div class="shrink-0 w-16 h-16 rounded-2xl bg-primary/10 text-primary grid place-items-center text-2xl font-bold font-[Poppins]">%</div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-[#090F3B]">{{ __('Got a discount coupon?') }}</h2>
                    <p class="text-gray-600 mt-1">{{ __('Apply your code at checkout and the discount is calculated for you.') }}</p>
                </div>
            </div>
            <a href="{{ auth()->check() ? route('products.index') : route('register') }}" class="btn btn-primary rounded-full px-8 font-[Poppins] font-semibold shrink-0">
                {{ auth()->check() ? __('Start shopping') : __('Sign up to shop') }}
            </a>
        </section>
    </div>
</x-layouts::shop>