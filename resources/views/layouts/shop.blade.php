<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        @include('partials.head')

        <!-- ฟอนต์ตาม Figma: Roboto (เนื้อหา), Poppins (ปุ่ม/เมนู) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    </head>
    <body class="min-h-svh bg-base-200 antialiased font-[Roboto] text-black">
        <!-- แถบสีด้านบน -->
        <header class="navbar bg-primary text-primary-content h-16 px-4 md:px-8 sticky top-0 z-10">
            <div class="flex-1">
                <a href="{{ route('home') }}" class="text-xl font-bold font-[Poppins]">CPE10</a>
            </div>

            <!-- เมนูผู้ใช้มุมขวา -->
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost gap-2 text-primary-content hover:bg-white/10 border-none">
                    <div class="avatar avatar-placeholder">
                        <div class="w-8 rounded-full bg-white text-primary">
                            <span class="text-xs font-bold">{{ auth()->user()->initials() }}</span>
                        </div>
                    </div>
                    <span class="hidden sm:inline font-[Poppins] font-normal">{{ auth()->user()->name }}</span>
                </div>
                <ul tabindex="0" class="dropdown-content menu bg-white text-black rounded-box z-20 mt-2 w-48 p-2 shadow font-[Poppins]">
                    <li><a href="{{ route('profile.edit') }}" wire:navigate>{{ __('My Account') }}</a></li>
                    <li><button type="submit" form="logout-form">{{ __('Logout') }}</button></li>
                </ul>
            </div>
            <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>
        </header>

        <main class="max-w-6xl mx-auto px-4 py-6 md:py-8">
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

