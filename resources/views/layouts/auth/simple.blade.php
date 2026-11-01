<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        @include('partials.head')

    </head>
    <body class="min-h-svh flex flex-col bg-white antialiased text-black">
        <!-- แถบสีด้านบน -->
      <header class="navbar bg-primary h-16 shrink-0 sticky top-0 z-10"></header>

         <main class="flex-1 flex items-center justify-center px-4 py-6">
            <!-- การ์ดกลางจอ -->
            <div class="card w-full max-w-md bg-white border border-primary/20 shadow-[0_4px_0_0_var(--color-primary)]">
                   <div class="card-body p-6 md:p-8">
                    {{ $slot }}
                </div>
            </div>
        </main>

        <!-- โลโก้มุมขวาล่าง: วางไฟล์รูปไว้ที่ public/images/cpe-logo.jpeg -->
        <img src="{{ asset('images/cpe-logo.jpeg') }}" alt="CPE logo" class="fixed bottom-6 right-6 w-12">

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
