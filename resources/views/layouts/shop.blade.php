<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        @include('partials.head')

        <!-- ฟอนต์ตาม Figma: Roboto (เนื้อหา), Poppins (ปุ่ม/เมนู) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    </head>
    <body class="min-h-svh flex flex-col bg-[#F4F5FB] antialiased font-[Roboto] text-black">
        @php
            // จำนวนชิ้นในตะกร้า (โชว์เป็นตัวเลขบนปุ่ม Cart) — ใช้ได้เมื่อมี model CartItem ของ backend แล้ว
            $cartCount = 0;
            $cartUrl = Route::has('cart.index') ? route('cart.index') : '#';
            $wishlistUrl = Route::has('wishlist.index') ? route('wishlist.index') : '#';
        @endphp

        <!-- ===== Navigation Bar ===== -->
        <header class="bg-white shadow-md sticky top-0 z-30">
            <div class="max-w-7xl mx-auto h-20 px-4 md:px-8 flex items-center gap-4 md:gap-8">
                <!-- โลโก้ -->
                <a href="{{ route('home') }}" class="shrink-0 flex items-center gap-2">
                    <img src="{{ asset('images/cpe-logo.jpeg') }}" alt="CPE" class="h-10 w-auto">
                    <span class="hidden lg:inline font-[Poppins] font-bold text-lg text-[#090F3B]">CPE10</span>
                </a>

                <!-- เมนูหลัก -->
                <nav class="hidden md:flex items-center gap-1 font-[Poppins] text-sm shrink-0">
                    <a href="{{ route('home') }}"
                       class="px-3 py-2 rounded-full transition {{ request()->routeIs('home') ? 'bg-[#090F3B] text-white' : 'text-[#090F3B] hover:bg-[#EEF0FB]' }}">
                        {{ __('Home') }}
                    </a>
                    <a href="{{ route('products.index') }}"
                       class="px-3 py-2 rounded-full transition {{ request()->routeIs('products.*') ? 'bg-[#090F3B] text-white' : 'text-[#090F3B] hover:bg-[#EEF0FB]' }}">
                        {{ __('Products') }}
                    </a>
                </nav>

                <!-- ช่องค้นหา: ส่งคำค้นไปหน้า Products (?search=...) -->
                <form action="{{ route('products.index') }}" method="GET" class="flex-1 min-w-0">
                    <label class="flex items-center gap-3 h-10 px-4 rounded-full bg-[#D9D9D9]/70 focus-within:bg-white focus-within:ring-2 focus-within:ring-primary transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7" />
                            <path stroke-linecap="round" d="m20 20-3.5-3.5" />
                        </svg>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Search products...') }}" class="w-full bg-transparent outline-none text-sm">
                    </label>
                </form>

                <div class="flex items-center gap-2 md:gap-4 shrink-0">
                    @auth
                        <!-- Wishlist -->
                        <a href="{{ $wishlistUrl }}" class="btn btn-circle border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white" aria-label="Wishlist" title="{{ __('Wish List') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linejoin="round" d="M12 20s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 10c0 5.65-7 10-7 10Z" />
                            </svg>
                        </a>

                        <!-- Cart + ตัวเลขจำนวนชิ้น -->
                        <a href="{{ $cartUrl }}" class="btn btn-circle border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white indicator" aria-label="Cart" title="{{ __('Cart') }}">
                            @if ($cartCount > 0)
                                <span class="indicator-item badge badge-sm border-none bg-[#E5484D] text-white">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                            @endif
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.4 10.2a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.76L20 8H6.2" />
                                <circle cx="9" cy="19" r="1.5" />
                                <circle cx="17" cy="19" r="1.5" />
                            </svg>
                        </a>

                        <!-- User (กดแล้วมีเมนู) -->
                        <div class="dropdown dropdown-end">
                            <div tabindex="0" role="button" class="btn btn-circle border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white" aria-label="Account">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="8" r="4" />
                                    <path stroke-linecap="round" d="M4 20c1.5-4 4.5-6 8-6s6.5 2 8 6" />
                                </svg>
                            </div>
                            <ul tabindex="0" class="dropdown-content menu bg-white rounded-box z-40 mt-2 w-56 p-2 shadow-lg font-[Poppins]">
                                <li class="menu-title">
                                    <span class="text-black truncate">{{ auth()->user()->name }}</span>
                                    <span class="text-xs font-normal text-gray-500 truncate">{{ auth()->user()->email }}</span>
                                </li>
                                <li><a href="{{ route('profile.edit') }}">{{ __('My Account') }}</a></li>
                                <li><a href="{{ $wishlistUrl }}">{{ __('Wish List') }}</a></li>
                                <li><a href="{{ $cartUrl }}">{{ __('Cart') }}</a></li>
                                <li class="border-t border-gray-100 mt-1 pt-1">
                                    <button type="submit" form="logout-form" class="text-[#E5484D]" data-test="logout-button">{{ __('Logout') }}</button>
                                </li>
                            </ul>
                        </div>
                    @else
                        <!-- ยังไม่ล็อกอิน: ปุ่ม Log in / Sign up -->
                        <a href="{{ route('login') }}" class="btn btn-ghost rounded-lg font-[Poppins] font-semibold text-[#090F3B] hidden sm:inline-flex">
                            {{ __('Log in') }}
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn rounded-lg border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white font-[Poppins] font-semibold">
                                {{ __('Sign up') }}
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- เมนูหลักบนจอเล็ก -->
            <nav class="md:hidden flex items-center gap-1 px-4 pb-3 font-[Poppins] text-sm">
                <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-full {{ request()->routeIs('home') ? 'bg-[#090F3B] text-white' : 'text-[#090F3B] bg-[#EEF0FB]' }}">{{ __('Home') }}</a>
                <a href="{{ route('products.index') }}" class="px-3 py-1.5 rounded-full {{ request()->routeIs('products.*') ? 'bg-[#090F3B] text-white' : 'text-[#090F3B] bg-[#EEF0FB]' }}">{{ __('Products') }}</a>
                @guest
                    <a href="{{ route('login') }}" class="sm:hidden ml-auto px-3 py-1.5 rounded-full text-[#090F3B] bg-[#EEF0FB]">{{ __('Log in') }}</a>
                @endguest
            </nav>

            @auth
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            @endauth
        </header>

        <!-- แจ้งเตือนจาก backend เช่น "เพิ่มลงตะกร้าแล้ว" (session('status') / session('error')) -->
        @if (session('status') || session('error'))
            <div class="max-w-7xl w-full mx-auto px-4 md:px-8 pt-6">
                <div role="alert" class="alert {{ session('error') ? 'alert-error' : 'alert-success' }} rounded-xl">
                    <span>{{ session('error') ?? session('status') }}</span>
                </div>
            </div>
        @endif

        <main class="flex-1 w-full max-w-7xl mx-auto px-4 md:px-8 py-8 md:py-12">
            {{ $slot }}
        </main>

        <!-- ===== Footer ===== -->
        <footer class="bg-[#090F3B] text-white/80 mt-8">
            <div class="max-w-7xl mx-auto px-4 md:px-8 py-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('images/cpe-logo.jpeg') }}" alt="CPE" class="h-10 w-auto rounded bg-white p-1">
                        <span class="font-[Poppins] font-bold text-xl text-white">CPE10 Shop</span>
                    </a>
                    <p class="mt-3 text-sm max-w-sm">
                        {{ __('Quality products, honest reviews, and great deals with discount coupons.') }}
                    </p>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-semibold text-white mb-3">{{ __('Shop') }}</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-white">{{ __('All Products') }}</a></li>
                        <li><a href="{{ $cartUrl }}" class="hover:text-white">{{ __('Cart') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-[Poppins] font-semibold text-white mb-3">{{ __('Account') }}</h3>
                    <ul class="space-y-2 text-sm">
                        @auth
                            <li><a href="{{ route('profile.edit') }}" class="hover:text-white">{{ __('My Account') }}</a></li>
                            <li><a href="{{ $wishlistUrl }}" class="hover:text-white">{{ __('Wish List') }}</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-white">{{ __('Log in') }}</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-white">{{ __('Sign up') }}</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/10">
                <p class="max-w-7xl mx-auto px-4 md:px-8 py-4 text-xs text-white/60">
                    &copy; {{ date('Y') }} CPE10 &middot; {{ __('Database Term Project') }}
                </p>
            </div>
        </footer>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
