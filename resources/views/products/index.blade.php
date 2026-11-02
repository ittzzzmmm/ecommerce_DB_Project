<x-layouts::shop :title="__('Products')">
    <!-- หัวข้อ + ค้นหา/เรียงลำดับ -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
        <div>
            <div class="breadcrumbs text-sm text-gray-500 p-0 mb-2">
                <ul>
                    <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                    <li>{{ __('Products') }}</li>
                </ul>
            </div>
            @if (request('search'))
                <h1 class="text-2xl md:text-4xl font-bold text-[#1F2937]">{{ __('Results for') }} “{{ request('search') }}”</h1>
            @else
                <h1 class="text-2xl md:text-4xl font-bold text-[#1F2937]">{{ __('All Products') }}</h1>
            @endif
            <p class="text-gray-500 mt-1">{{ trans_choice(':count product|:count products', $products->total(), ['count' => $products->total()]) }}</p>
        </div>

        <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2">
            @if (request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            <label for="sort" class="text-sm text-gray-600 shrink-0">{{ __('Sort by') }}</label>
            <select id="sort" name="sort" onchange="this.form.submit()" class="select select-sm rounded-lg bg-white w-44">
                @foreach (['recommended' => __('Recommended'), 'newest' => __('Newest'), 'price_asc' => __('Price: Low to High'), 'price_desc' => __('Price: High to Low')] as $value => $label)
                    <option value="{{ $value }}" @selected(request('sort', 'recommended') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @if (request('search'))
                <a href="{{ route('products.index') }}" class="btn btn-sm btn-ghost rounded-lg">{{ __('Clear') }}</a>
            @endif
        </form>
    </div>

    @if ($products->isEmpty())
        <div class="bg-white rounded-2xl py-16 px-6 text-center shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <circle cx="11" cy="11" r="7" /><path stroke-linecap="round" d="m20 20-3.5-3.5" />
            </svg>
            <p class="mt-4 text-lg font-bold text-[#090F3B]">{{ __('No products found') }}</p>
            <p class="text-gray-500 mt-1">{{ __('Try a different keyword.') }}</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary rounded-full mt-6">{{ __('See all products') }}</a>
        </div>
    @else
        <!-- Grid สินค้า (ตาม Figma: 5 คอลัมน์) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>

        <!-- Pagination: <  1  2  3  > -->
        @if ($products->hasPages())
            @php
                $current = $products->currentPage();
                $last = $products->lastPage();
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);
            @endphp
            <nav class="flex justify-center items-center gap-1 mt-10" aria-label="Pagination">
                <a href="{{ $products->previousPageUrl() ?? '#' }}" class="btn btn-sm btn-ghost w-10 {{ $products->onFirstPage() ? 'btn-disabled' : '' }}">&lt;</a>
                @if ($start > 1)
                    <a href="{{ $products->url(1) }}" class="btn btn-sm btn-ghost w-10">1</a>
                    @if ($start > 2)<span class="px-2">…</span>@endif
                @endif
                @for ($page = $start; $page <= $end; $page++)
                    <a href="{{ $products->url($page) }}"
                       class="btn btn-sm w-10 {{ $page === $current ? 'bg-black text-white border-black hover:bg-black' : 'btn-ghost' }}">{{ $page }}</a>
                @endfor
                @if ($end < $last)
                    @if ($end < $last - 1)<span class="px-2">…</span>@endif
                    <a href="{{ $products->url($last) }}" class="btn btn-sm btn-ghost w-10">{{ $last }}</a>
                @endif
                <a href="{{ $products->nextPageUrl() ?? '#' }}" class="btn btn-sm btn-ghost w-10 {{ $products->hasMorePages() ? '' : 'btn-disabled' }}">&gt;</a>
            </nav>
        @endif
    @endif
</x-layouts::shop>
