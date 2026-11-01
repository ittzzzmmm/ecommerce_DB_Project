<x-layouts::shop :title="__('Order').' #'.$order->id">
    {{-- ตัวแปรจาก OrderController@show: $order (items.product, couponUsage.coupon), $reviewedProductIds --}}

    <div class="breadcrumbs text-sm text-gray-500 p-0 mb-4">
        <ul>
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li><a href="{{ route('orders.index') }}">{{ __('My Purchase') }}</a></li>
            <li class="text-gray-700 font-medium">{{ __('Order') }} #{{ $order->id }}</li>
        </ul>
    </div>

    <div class="flex flex-wrap items-end justify-between gap-3 mb-8">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-[#090F3B]">{{ __('Order') }} #{{ $order->id }}</h1>
                @include('orders._status', ['status' => $order->status])
            </div>
            <p class="mt-1 text-[#5A6BD5]">{{ __('Placed on') }} {{ $order->created_at?->format('d M Y, H:i') }}</p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-[#5A6BD5] hover:underline">← {{ __('All orders') }}</a>
    </div>

    <div class="grid lg:grid-cols-[1fr_360px] gap-8 items-start">

        <!-- รายการสินค้าในออเดอร์ + ปุ่มไปรีวิว -->
        <section class="bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-6 md:p-8">
            <h2 class="text-lg font-semibold text-[#090F3B] mb-5">{{ __('Items') }}</h2>

            <div class="divide-y divide-gray-100">
                @foreach ($order->items as $item)
                    @php
                        $product = $item->product;
                        $reviewed = in_array($item->product_id, $reviewedProductIds);
                    @endphp
                    <div class="py-4 first:pt-0 last:pb-0 flex flex-wrap sm:flex-nowrap items-center gap-4" data-test="order-item">
                        <a href="{{ route('products.show', $item->product_id) }}" class="w-16 h-16 rounded-lg border border-gray-200 bg-[#F8F9FD] grid place-items-center shrink-0 overflow-hidden">
                            @if ($product?->image)
                                <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" /><path d="m21 15-5-5L5 21" />
                                </svg>
                            @endif
                        </a>

                        <div class="min-w-0 flex-1">
                            <a href="{{ route('products.show', $item->product_id) }}" class="font-semibold text-[#090F3B] hover:underline leading-snug">
                                {{ $product?->name ?? __('Product unavailable') }}
                            </a>
                            <p class="text-sm text-[#5A6BD5] tabular-nums">{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                        </div>

                        <div class="flex items-center gap-4 ml-auto">
                            <span class="font-bold text-[#090F3B] tabular-nums">{{ number_format($item->subtotal, 2) }}</span>

                            <!-- CTA ไปรีวิวสินค้านี้ -->
                            @if ($order->status === 'cancelled')
                                {{-- ออเดอร์ที่ยกเลิกแล้วรีวิวไม่ได้ --}}
                            @elseif ($reviewed)
                                <span class="inline-flex items-center gap-1 text-sm text-[#067647] font-medium" data-test="item-reviewed">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                    </svg>
                                    {{ __('Reviewed') }}
                                </span>
                            @elseif ($product)
                                <a href="{{ route('products.show', $item->product_id) }}#reviews" data-test="write-review"
                                   class="btn btn-sm rounded-full border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white px-4">
                                    {{ __('Write a review') }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- สรุปยอด + ที่อยู่จัดส่ง -->
        <aside class="space-y-6">
            <section class="bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-6 md:p-7">
                <h2 class="text-lg font-semibold text-[#090F3B] mb-5">{{ __('Order Summary') }}</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between text-[#5A6BD5]">
                        <dt>{{ __('Subtotal') }}</dt>
                        <dd class="font-semibold text-gray-800 tabular-nums">{{ number_format($order->total_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-[#5A6BD5]">
                        <dt>
                            {{ __('Discount') }}
                            @if ($order->couponUsage?->coupon)
                                <span class="ml-1 rounded bg-[#EEF0FB] px-1.5 py-0.5 text-xs font-semibold text-[#3B4BB0]">{{ $order->couponUsage->coupon->code }}</span>
                            @endif
                        </dt>
                        <dd class="font-semibold text-gray-800 tabular-nums">−{{ number_format($order->discount_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-[#5A6BD5]">
                        <dt>{{ __('Shipping') }}</dt>
                        <dd class="font-semibold text-gray-800">{{ __('Free') }}</dd>
                    </div>
                </dl>
                <div class="my-5 border-t border-gray-100"></div>
                <div class="flex items-baseline justify-between">
                    <span class="font-semibold text-[#090F3B]">{{ __('Total') }}</span>
                    <span class="text-2xl font-bold text-[#E5484D] tabular-nums">{{ number_format($order->final_amount, 2) }}</span>
                </div>
            </section>

            <section class="bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-6 md:p-7">
                <h2 class="text-lg font-semibold text-[#090F3B] mb-3">{{ __('Shipping Address') }}</h2>
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $order->shipping_address }}</p>
            </section>

            <a href="{{ route('products.index') }}" class="btn w-full rounded-xl border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white font-semibold">
                {{ __('Continue Shopping') }}
            </a>
        </aside>
    </div>
</x-layouts::shop>
