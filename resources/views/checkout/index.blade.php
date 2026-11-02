<x-layouts::shop :title="__('Checkout')">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs text-sm text-gray-500 p-0 mb-4">
        <ul>
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li><a href="{{ route('cart.index') }}">{{ __('Cart') }}</a></li>
            <li class="text-gray-700 font-medium">{{ __('Checkout') }}</li>
        </ul>
    </div>

    <!-- Title -->
    <h1 class="text-3xl md:text-4xl font-extrabold text-[#090F3B] mb-8">{{ __('Checkout') }}</h1>

    @error('cart')
        <div role="alert" class="alert alert-error mb-6 rounded-xl">{{ $message }}</div>
    @enderror

    <form method="POST" action="{{ route('checkout.coupon') }}" class="mb-6 flex max-w-xl items-start gap-3">
        @csrf
        <div class="flex-1">
            <label for="coupon_code" class="sr-only">{{ __('Coupon code') }}</label>
            <input id="coupon_code" type="text" name="coupon_code" value="{{ old('coupon_code', $couponCode) }}"
                   placeholder="{{ __('Coupon code') }}" class="input input-bordered w-full rounded-lg">
            @error('coupon_code')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="btn rounded-lg">{{ __('Apply coupon') }}</button>
    </form>

    <form method="POST" action="{{ route('checkout.store') }}" class="grid lg:grid-cols-[1fr_360px] gap-8 items-start">
        @csrf

        <!-- คอลัมน์ซ้าย: ข้อมูลจัดส่ง + สรุปรายการสินค้า -->
        <div class="space-y-6">
            
            <!-- Shipping Address Card -->
            <div class="bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-6 md:p-8">
                <h2 class="text-lg font-bold text-[#090F3B] mb-5">{{ __('Shipping Address') }}</h2>

                <div class="grid sm:grid-cols-2 gap-4 mb-4 text-sm">
                    <div>
                        <span class="text-xs text-[#5A6BD5] font-medium block mb-1">{{ __('Name') }}</span>
                        <p class="font-semibold text-gray-800">{{ $user->name ?? 'Guest User' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-[#5A6BD5] font-medium block mb-1">{{ __('Email') }}</span>
                        <p class="font-semibold text-gray-800">{{ $user->email ?? 'guest@example.com' }}</p>
                    </div>
                </div>

                <div>
                    <label for="address" class="text-xs text-[#5A6BD5] font-medium block mb-2">{{ __('Address') }}</label>
                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        required
                        autocomplete="street-address"
                        placeholder="House no, street, district, postal code, phone number"
                        class="textarea w-full rounded-xl border border-gray-300 focus:border-[#5A6BD5] focus:ring-1 focus:ring-[#5A6BD5] text-sm text-gray-700 placeholder:text-gray-400 leading-relaxed">{{ old('address', $defaultAddress ?? '') }}</textarea>
                    @error('address')
                        <p class="text-xs text-[#E5484D] mt-1">{{ $message }}</p>
                    @enderror
                    @if ($addressSource)
                        <p class="text-xs text-gray-500 mt-2 leading-relaxed" data-test="address-prefilled">
                            {{ $addressSource === 'profile'
                                ? __('Filled in from your profile. Edits here apply to this order only.')
                                : __('Filled in from your last order. Edits here apply to this order only.') }}
                        </p>
                    @endif

                    <!-- ตั้งเป็นที่อยู่เริ่มต้นในโปรไฟล์ (ติ๊กไว้ให้ถ้ายังไม่เคยตั้ง) -->
                    <label class="mt-3 flex items-center gap-2 text-sm text-gray-700 cursor-pointer w-fit">
                        <input type="checkbox" name="save_as_default" value="1" class="checkbox checkbox-sm checkbox-primary"
                               @checked(old('save_as_default', $addressSource !== 'profile')) data-test="save-as-default">
                        {{ __('Save as default address') }}
                    </label>
                </div>
            </div>

            <!-- Order Items Card -->
            <div class="bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-6 md:p-8">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-[#090F3B]">{{ __('Order Items') }} <span class="text-sm font-medium text-[#5A6BD5]">({{ collect($cart)->sum('quantity') }})</span></h2>
                    <a href="{{ route('cart.index') }}" class="text-xs font-semibold text-[#5A6BD5] hover:underline">
                        {{ __('Edit cart') }}
                    </a>
                </div>

                <div class="divide-y divide-gray-100 max-h-[26rem] overflow-y-auto overscroll-contain pr-2 -mr-2" data-test="checkout-items">
                    @foreach ($cart as $item)
                        <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="w-14 h-14 rounded-lg border border-gray-200 bg-[#F8F9FD] grid place-items-center shrink-0 overflow-hidden">
                                    @if (!empty($item['image']))
                                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" /><path d="m21 15-5-5L5 21" />
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-[#090F3B] text-sm truncate">{{ $item['name'] }}</h3>
                                    <span class="text-xs text-[#5A6BD5]">{{ number_format($item['price'], 2) }} x {{ $item['quantity'] }}</span>
                                </div>
                            </div>
                            <span class="font-bold text-[#090F3B] text-sm shrink-0">
                                {{ number_format($item['price'] * $item['quantity'], 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- คอลัมน์ขวา: Order Summary & Place Order -->
        <div class="lg:sticky lg:top-28 bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-6 md:p-7">
            <h2 class="text-xl font-bold text-[#090F3B] mb-5">{{ __('Order Summary') }}</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between text-[#5A6BD5]">
                    <span>{{ __('Subtotal') }}</span>
                    <span class="font-semibold text-gray-800">{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-[#5A6BD5]">
                    <span>{{ __('Discount') }}</span>
                    <span class="font-semibold text-gray-800">{{ number_format($discount, 2) }}</span>
                </div>
                <div class="flex justify-between text-[#5A6BD5]">
                    <span>{{ __('Shipping') }}</span>
                    <span class="font-semibold text-gray-800">{{ $shipping == 0 ? __('Free') : number_format($shipping, 2) }}</span>
                </div>
            </div>

            <div class="my-5 border-t border-gray-100"></div>

            <div class="flex items-baseline justify-between mb-6">
                <span class="font-bold text-[#090F3B]">{{ __('Total') }}</span>
                <span class="text-2xl font-black text-[#E5484D]">{{ number_format($total, 2) }}</span>
            </div>

            <button type="submit" class="btn w-full rounded-xl border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white font-semibold py-3 shadow-md">
                {{ __('Place Order') }}
            </button>

            <div class="text-center mt-4">
                <a href="{{ route('cart.index') }}" class="text-xs text-[#5A6BD5] hover:underline font-medium">
                    {{ __('Back to cart') }}
                </a>
            </div>
        </div>

    </form>
</x-layouts::shop>
