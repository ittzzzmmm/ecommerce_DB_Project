<x-layouts::shop :title="__('Shopping Cart')">
    @php
        $cart = session()->get('cart', []);
        $itemCount = collect($cart)->sum('quantity');
        $subtotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $shipping = 0;
        $total = $subtotal;
    @endphp

    <!-- Breadcrumbs -->
    <div class="breadcrumbs text-sm text-gray-500 font-[Poppins] p-0 mb-4">
        <ul>
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li class="text-gray-700 font-medium">{{ __('Cart') }}</li>
        </ul>
    </div>

    <!-- Header Title & Item Count -->
    <div class="flex items-baseline justify-between mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-[#090F3B] font-[Poppins]">{{ __('Shopping Cart') }}</h1>
        <span class="text-sm text-[#5A6BD5] font-medium font-[Poppins]">{{ $itemCount }} {{ Str::plural('item', $itemCount) }}</span>
    </div>

    @if (empty($cart))
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-12 text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-[#F4F5FB] grid place-items-center text-[#5A6BD5] mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.4 10.2a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.76L20 8H6.2" />
                    <circle cx="9" cy="19" r="1.5" /><circle cx="17" cy="19" r="1.5" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-[#090F3B]">{{ __('Your cart is empty') }}</h2>
            <p class="text-gray-500 mt-1 mb-6">{{ __('Looks like you haven\'t added any items yet.') }}</p>
            <a href="{{ route('products.index') }}" class="btn rounded-full bg-[#090F3B] hover:bg-[#1E2A6E] text-white px-8">
                {{ __('Explore Products') }}
            </a>
        </div>
    @else
        @error('cart')
            <div role="alert" class="alert alert-error mb-6 rounded-xl">{{ $message }}</div>
        @enderror
        <!-- Cart Content Layout -->
        <div class="grid lg:grid-cols-[1fr_360px] gap-8 items-start">
            
            <!-- ตารางรายการสินค้า -->
            <div class="bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-6 md:p-8">
                <!-- Table Header -->
                <div class="grid grid-cols-[1fr_90px_110px_90px] text-xs font-semibold text-[#5A6BD5] uppercase tracking-wider pb-4 border-b border-gray-100">
                    <div>{{ __('Product') }}</div>
                    <div class="text-center">{{ __('Price') }}</div>
                    <div class="text-center">{{ __('Quantity') }}</div>
                    <div class="text-right">{{ __('Total') }}</div>
                </div>

                <!-- รายการแถวสินค้า -->
                <div class="divide-y divide-gray-100">
                    @foreach ($cart as $id => $item)
                        <div class="grid grid-cols-[1fr_90px_110px_90px] items-center py-5">
                            
                            <!-- สินค้า & รูป -->
                            <div class="flex items-center gap-4 pr-3">
                                <div class="w-16 h-16 rounded-lg border border-gray-200 bg-[#F8F9FD] grid place-items-center shrink-0 overflow-hidden relative">
                                    @if (!empty($item['image']))
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" /><path d="m21 15-5-5L5 21" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-[#090F3B] truncate text-sm md:text-base">{{ $item['name'] }}</h3>
                                    <form method="POST" action="{{ route('cart.remove', $id) }}" class="mt-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-gray-400 hover:text-[#E5484D] transition">
                                            {{ __('Remove') }}
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- ราคาต่อชิ้น -->
                            <div class="text-center text-sm font-semibold text-gray-700">
                                {{ number_format($item['price']) }}
                            </div>

                            <!-- ปรับจำนวน < (qty) > -->
                            <div class="flex items-center justify-center gap-1 font-[Roboto]">
                                <form method="POST" action="{{ route('cart.update', $id) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}">
                                    <button type="submit" aria-label="{{ __('Decrease quantity') }}" @disabled($item['quantity'] <= 1)
                                            class="text-[#5A6BD5] hover:text-[#090F3B] disabled:opacity-30 px-1 text-sm font-bold">−</button>
                                </form>

                                <span class="w-7 h-7 rounded-full border border-[#B9C2FF] text-[#5A6BD5] text-xs font-semibold grid place-items-center">
                                    {{ $item['quantity'] }}
                                </span>

                                <form method="POST" action="{{ route('cart.update', $id) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                    <button type="submit" aria-label="{{ __('Increase quantity') }}" @disabled($item['quantity'] >= $item['stock'])
                                            class="text-[#5A6BD5] hover:text-[#090F3B] disabled:opacity-30 px-1 text-sm font-bold">+</button>
                                </form>
                            </div>

                            <!-- ราคารวมของแถวนั้น (สีแดงตาม Figma) -->
                            <div class="text-right font-bold text-[#E5484D] text-sm md:text-base">
                                {{ number_format($item['price'] * $item['quantity']) }}
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <!-- กล่อง Order Summary ด้านขวา -->
            <div class="bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-6 md:p-7">
                <h2 class="text-xl font-bold text-[#090F3B] mb-5 font-[Poppins]">{{ __('Order Summary') }}</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-[#5A6BD5]">
                        <span>{{ __('Subtotal') }} ({{ $itemCount }} {{ Str::plural('item', $itemCount) }})</span>
                        <span class="font-semibold text-gray-800">{{ number_format($subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-[#5A6BD5]">
                        <span>{{ __('Shipping') }}</span>
                        <span class="font-semibold text-gray-800">{{ $shipping == 0 ? __('Free') : number_format($shipping) }}</span>
                    </div>
                </div>

                <div class="my-5 border-t border-gray-100"></div>

                <div class="flex items-baseline justify-between mb-3">
                    <span class="font-bold text-[#090F3B]">{{ __('Total') }}</span>
                    <span class="text-2xl font-black text-[#E5484D]">{{ number_format($total) }}</span>
                </div>

                <p class="text-xs text-[#5A6BD5] mb-6">
                    {{ __('Have a coupon? Apply it at checkout.') }}
                </p>

                <a href="{{ route('checkout.index') }}" class="btn w-full rounded-xl border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white font-[Poppins] font-semibold py-3 shadow-md">
                    {{ __('Proceed to Checkout') }}
                </a>

                <div class="text-center mt-4">
                    <a href="{{ route('products.index') }}" class="text-xs text-[#5A6BD5] hover:underline font-medium">
                        {{ __('Continue Shopping') }}
                    </a>
                </div>
            </div>

        </div>
    @endif
</x-layouts::shop>
