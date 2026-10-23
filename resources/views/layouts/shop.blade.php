<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        @include('partials.head')

        <!-- ฟอนต์ตาม Figma: Roboto (เนื้อหา), Poppins (ปุ่ม/เมนู) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    </head>
    <body class="min-h-svh bg-white antialiased font-[Roboto] text-black">
        <!-- Navbar สีขาว + เงาด้านล่าง -->
        <header class="bg-white shadow-md sticky top-0 z-10">
            <div class="max-w-7xl mx-auto h-20 px-4 md:px-8 flex items-center gap-4 md:gap-10">
                <!-- โลโก้ -->
                <a href="{{ route('home') }}" class="shrink-0" wire:navigate>
                    <img src="{{ asset('images/cpe-logo.jpeg') }}" alt="CPE" class="h-10 w-auto">
                </a>

                <!-- ช่องค้นหา (ยังไม่เชื่อมระบบค้นหา) -->
                <label class="flex-1 flex items-center gap-3 h-10 px-4 rounded-full bg-[#D9D9D9]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" d="m20 20-3.5-3.5" />
                    </svg>
                    <input type="search" placeholder="{{ __('Search') }}" class="w-full bg-transparent outline-none text-sm">
                </label>

                <!-- ปุ่มกลมด้านขวา -->
                <div class="flex items-center gap-3 md:gap-5 shrink-0">
                    <!-- Wishlist -->
                    <a href="#" class="btn btn-circle border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white" aria-label="Wishlist">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linejoin="round" d="M12 20s-7-4.35-7-10a4 4 0 0 1 7-2.65A4 4 0 0 1 19 10c0 5.65-7 10-7 10Z" />
                        </svg>
                    </a>

                    <!-- Cart -->
                    <a href="#" class="btn btn-circle border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white" aria-label="Cart">
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
                        <ul tabindex="0" class="dropdown-content menu bg-white rounded-box z-20 mt-2 w-48 p-2 shadow font-[Poppins]">
                            <li class="menu-title text-black truncate">{{ auth()->user()->name }}</li>
                            <li><a href="{{ route('profile.edit') }}" wire:navigate>{{ __('My Account') }}</a></li>
                            <li><button type="submit" form="logout-form">{{ __('Logout') }}</button></li>
                        </ul>
                    </div>
                </div>
            </div>
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>
        </header>

        <main class="max-w-7xl mx-auto px-4 md:px-8 py-8 md:py-14">
            {{ $slot }}
        </main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>