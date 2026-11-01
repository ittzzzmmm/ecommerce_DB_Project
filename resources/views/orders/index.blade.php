<x-layouts::shop :title="__('My Purchase')">
    {{-- ตัวแปรจาก OrderController@index: $orders (paginate, แต่ละอันมี items.product, item_count) --}}

    <div class="breadcrumbs text-sm text-gray-500 p-0 mb-4">
        <ul>
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li><a href="{{ route('profile.edit') }}">{{ __('My Account') }}</a></li>
            <li class="text-gray-700 font-medium">{{ __('My Purchase') }}</li>
        </ul>
    </div>

    <div class="flex items-baseline justify-between mb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-[#090F3B]">{{ __('My Purchase') }}</h1>
            <p class="mt-1 text-[#5A6BD5] leading-relaxed">{{ __('Your orders and what you bought') }}</p>
        </div>
        @if ($orders->total() > 0)
            <span class="text-sm text-[#5A6BD5] font-medium">{{ $orders->total() }} {{ Str::plural('order', $orders->total()) }}</span>
        @endif
    </div>

    @if ($orders->isEmpty())
        <div class="bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-12 text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-[#F4F5FB] grid place-items-center text-[#5A6BD5] mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-[#090F3B]">{{ __('No orders yet') }}</h2>
            <p class="text-gray-500 mt-1 mb-6 leading-relaxed">{{ __('When you place an order, it will show up here.') }}</p>
            <a href="{{ route('products.index') }}" class="btn rounded-full border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white px-8">
                {{ __('Explore Products') }}
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <a href="{{ route('orders.show', $order) }}" data-test="order-card"
                   class="block bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-5 md:p-6 hover:-translate-y-0.5 transition">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="font-semibold text-[#090F3B]">{{ __('Order') }} #{{ $order->id }}</span>
                            @include('orders._status', ['status' => $order->status])
                        </div>
                        <span class="text-sm text-gray-500">{{ $order->created_at?->format('d M Y, H:i') }}</span>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-4">
                        <!-- รูปสินค้า (สูงสุด 4 รูป) -->
                        <div class="flex items-center gap-2 min-w-0">
                            @foreach ($order->items->take(4) as $item)
                                @php($image = $item->product?->image)
                                <div class="w-12 h-12 rounded-lg border border-gray-200 bg-[#F8F9FD] grid place-items-center shrink-0 overflow-hidden" title="{{ $item->product?->name }}">
                                    @if ($image)
                                        <img src="{{ str_starts_with($image, 'http') ? $image : asset('storage/'.$image) }}" alt="{{ $item->product?->name }}" class="w-full h-full object-cover">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <rect x="3" y="3" width="18" height="18" rx="2" /><circle cx="8.5" cy="8.5" r="1.5" /><path d="m21 15-5-5L5 21" />
                                        </svg>
                                    @endif
                                </div>
                            @endforeach
                            @if ($order->items->count() > 4)
                                <span class="text-sm text-gray-500 pl-1">+{{ $order->items->count() - 4 }}</span>
                            @endif
                        </div>

                        <div class="text-right shrink-0">
                            <p class="text-xs text-[#5A6BD5]">{{ (int) $order->item_count }} {{ Str::plural('item', (int) $order->item_count) }}</p>
                            <p class="text-lg font-bold text-[#E5484D] tabular-nums">{{ number_format($order->final_amount, 2) }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</x-layouts::shop>
