@props(['product'])

@php
    // รูปจากคอลัมน์ products.image (เก็บใน storage/app/public) — ถ้าไม่มีรูปจะโชว์ไอคอนแทน
    $image = $product->image
        ? (str_starts_with($product->image, 'http') ? $product->image : asset('storage/'.$product->image))
        : null;
    $rating = $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : null;
    $sold = (int) ($product->sold ?? 0);
@endphp

<a href="{{ route('products.show', $product->id) }}"
   {{ $attributes->merge(['class' => 'group flex flex-col bg-white text-black rounded-xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition duration-200']) }}>
    <!-- รูปสินค้า -->
    <div class="relative aspect-square bg-[#F8F9FD] border-b border-slate-100 grid place-items-center overflow-hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
            <rect x="3" y="3" width="18" height="18" rx="2" />
            <circle cx="8.5" cy="8.5" r="1.5" />
            <path d="m21 15-5-5L5 21" />
        </svg>
        @if ($image)
            <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy" onerror="this.remove()"
                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-300">
        @endif

        @if ($product->stock < 1)
            <span class="absolute top-2 left-2 badge badge-sm border-none bg-gray-800 text-white">{{ __('Sold out') }}</span>
        @elseif ($product->stock <= 5)
            <span class="absolute top-2 left-2 badge badge-sm border-none bg-[#E5484D] text-white">{{ __('Only :n left', ['n' => $product->stock]) }}</span>
        @endif
    </div>

    <!-- ข้อมูลสินค้า -->
    <div class="flex flex-col flex-1 p-3 gap-2">
        <h3 class="text-sm leading-snug line-clamp-2 min-h-[2.5rem] group-hover:text-primary">{{ $product->name }}</h3>
        <p class="text-lg font-bold text-[#F00000]">฿{{ number_format($product->price) }}</p>
        <div class="mt-auto flex items-center justify-between text-xs text-gray-700">
            <span>{{ __('sold') }} {{ number_format($sold) }}</span>
            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md border border-[#F5C518] bg-[#FFF6D6]">
                {{ $rating ?? '-' }}
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#F5C518]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2.5l2.9 6.1 6.6.8-4.9 4.6 1.3 6.6L12 17.3l-5.9 3.3 1.3-6.6L2.5 9.4l6.6-.8z" />
                </svg>
            </span>
        </div>
    </div>
</a>
