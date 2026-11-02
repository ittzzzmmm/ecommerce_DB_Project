<x-layouts::app :title="__('Products')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 dark:text-white">
                Products
            </h1>

            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                Browse our products
            </p>
        </div>
<div class="mx-auto w-full max-w-6xl">
        {{-- Product Grid --}}
        @if ($products->isNotEmpty())
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6">

                @foreach ($products as $product)
                    <div
                        class="flex items-center gap-3 overflow-hidden rounded-xl border border-neutral-200 p-3
                               bg-white shadow-sm transition hover:shadow-md
                               dark:border-neutral-700 dark:bg-neutral-900"
                    >
                        {{-- Product Image Placeholder --}}
                        <div class="flex size-16 shrink-0 items-center justify-center rounded-lg bg-neutral-100 dark:bg-neutral-800">
                            <span class="text-xs text-neutral-400">
                                No image
                            </span>
                        </div>

                        {{-- Product Information --}}
                        <div class="flex min-w-0 flex-1 flex-col gap-1.5">

                            {{-- Product Name --}}
                            <h2 class="line-clamp-1 text-sm font-semibold text-neutral-900 dark:text-white">
                                {{ $product->name }}
                            </h2>

                            {{-- Description --}}
                            @if ($product->description)
                                <p class="line-clamp-1 text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ $product->description }}
                                </p>
                            @endif

                            {{-- Rating --}}
                            <div class="flex items-center gap-1.5">
                                <span class="text-yellow-500">★</span>

                                @if ($product->reviews_avg_rating !== null)
                                    <span class="text-xs text-neutral-600 dark:text-neutral-300">
                                        {{ number_format($product->reviews_avg_rating, 1) }}
                                    </span>
                                @else
                                    <span class="text-xs text-neutral-400">
                                        No reviews
                                    </span>
                                @endif
                            </div>

                            {{-- Price --}}
                            <div class="text-sm font-bold text-neutral-900 dark:text-white">
                                ฿{{ number_format($product->price, 2) }}
                            </div>

                            {{-- Action Button --}}
                            <button
                                type="button"
                                class="mt-1 w-full rounded-lg bg-neutral-900 px-2 py-1.5
                                       text-xs font-medium text-white transition
                                       hover:bg-neutral-700
                                       dark:bg-white dark:text-neutral-900
                                       dark:hover:bg-neutral-200"
                            >
                                View Product
                            </button>

                        </div>
                    </div>
                @endforeach

            </div>
        @else
            {{-- Empty State --}}
            <div class="rounded-xl border border-dashed border-neutral-300 p-12 text-center dark:border-neutral-700">
                <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">
                    No products found
                </h2>

                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                    There are currently no products available.
                </p>
            </div>
        @endif
</div>
    </div>
</x-layouts::app>
