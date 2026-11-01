<div class="bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-6 md:p-10 font-[Poppins]">
    
    <!-- ส่วนหัวข้อ & สรุปคะแนน -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-gray-100">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-[#090F3B]">{{ __('Reviews & Ratings') }}</h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ __('Real customer reviews from verified purchases') }}
            </p>
        </div>

        <div class="flex items-center gap-3 bg-[#F8F9FD] px-5 py-3 rounded-xl border border-[#B9C2FF]/40">
            <span class="text-3xl font-extrabold text-[#090F3B]">
                {{ $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : '-' }}
            </span>
            <div>
                <div class="flex text-[#F5C518]">
                    @for ($s = 1; $s <= 5; $s++)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 {{ $s <= round($product->reviews_avg_rating ?? 0) ? 'fill-current' : 'text-gray-300' }}" viewBox="0 0 24 24">
                            <path d="M12 2.5l2.9 6.1 6.6.8-4.9 4.6 1.3 6.6L12 17.3l-5.9 3.3 1.3-6.6L2.5 9.4l6.6-.8z" />
                        </svg>
                    @endfor
                </div>
                <span class="text-xs text-[#5A6BD5]">{{ $product->reviews_count ?? $product->reviews?->count() ?? 0 }} {{ __('reviews') }}</span>
            </div>
        </div>
    </div>

    <!-- ฟอร์มเขียนรีวิว -->
    <div class="my-8 bg-[#F8F9FD] p-6 rounded-xl border border-gray-100">
        <h3 class="font-bold text-[#090F3B] mb-2">{{ __('Write a Review') }}</h3>

        @auth
            @if ($hasReviewed ?? false)
                <!-- เคยรีวิวสินค้านี้แล้ว: 1 คนรีวิวได้ 1 ครั้งต่อสินค้า -->
                <div role="alert" class="flex items-start gap-3 rounded-xl border border-[#B9C2FF] bg-[#EEF0FB] px-4 py-3 text-[#3B4BB0]" data-test="review-already">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                    </svg>
                    <div class="text-sm">
                        <p class="font-semibold">{{ __('You have already reviewed this product.') }}</p>
                        <p>{{ __('Each customer can post one review per product.') }}</p>
                    </div>
                </div>
            @elseif (! ($hasPurchased ?? false))
                <!-- ยังไม่เคยซื้อ: รีวิวไม่ได้ -->
                <div role="alert" class="flex items-start gap-3 rounded-xl border border-[#FEDF89] bg-[#FFFAEB] px-4 py-3 text-[#B54708]" data-test="review-locked">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" />
                    </svg>
                    <div class="text-sm">
                        <p class="font-semibold">{{ __('Only customers who bought this product can review it.') }}</p>
                        <p>{{ __('Place an order for this product, then come back to share your review.') }}</p>
                    </div>
                </div>
            @else
            <form method="POST" action="{{ route('reviews.store', $product->id) }}" class="space-y-4">
                @csrf

                <!-- เลือกดาว 1-5 (DaisyUI Rating) -->
                <div>
                    <label class="block text-xs font-semibold text-[#5A6BD5] uppercase mb-1">{{ __('Rating') }}</label>
                    <div class="rating rating-md gap-1">
                        <input type="radio" name="rating" value="1" class="mask mask-star-2 bg-[#F5C518]" required />
                        <input type="radio" name="rating" value="2" class="mask mask-star-2 bg-[#F5C518]" />
                        <input type="radio" name="rating" value="3" class="mask mask-star-2 bg-[#F5C518]" />
                        <input type="radio" name="rating" value="4" class="mask mask-star-2 bg-[#F5C518]" />
                        <input type="radio" name="rating" value="5" class="mask mask-star-2 bg-[#F5C518]" checked />
                    </div>
                    @error('rating')
                        <p class="text-xs text-[#E5484D] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- กล่องข้อความ Comment -->
                <div>
                    <label for="comment" class="block text-xs font-semibold text-[#5A6BD5] uppercase mb-1">{{ __('Your Comment') }}</label>
                    <textarea 
                        id="comment" 
                        name="comment" 
                        rows="3" 
                        required
                        placeholder="{{ __('Share your experience with this product...') }}"
                        class="textarea w-full rounded-xl border-gray-300 focus:border-[#5A6BD5] focus:ring-1 focus:ring-[#5A6BD5] text-sm text-gray-700 bg-white leading-relaxed">{{ old('comment') }}</textarea>
                    @error('comment')
                        <p class="text-xs text-[#E5484D] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-sm md:btn-md rounded-xl border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white px-6 font-semibold">
                        {{ __('Submit Review') }}
                    </button>
                </div>
            </form>
            @endif
        @else
            <div class="text-center py-4">
                <p class="text-sm text-gray-600 mb-3">{{ __('Please login to rate and leave a comment.') }}</p>
                <a href="{{ route('login') }}" class="btn btn-sm rounded-xl border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white px-6">
                    {{ __('Login to Review') }}
                </a>
            </div>
        @endauth
    </div>

    <!-- รายการรีวิวที่ผ่านมา -->
    <div class="divide-y divide-gray-100">
        @forelse ($product->reviews ?? [] as $rev)
            <div class="py-5 first:pt-0 last:pb-0">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-[#090F3B] text-white text-xs font-bold grid place-items-center">
                            {{ strtoupper(substr($rev->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <span class="text-sm font-bold text-gray-800">{{ $rev->user->name ?? 'Anonymous' }}</span>
                            <span class="text-xs text-gray-400 block">{{ $rev->created_at?->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- ดาวที่ได้ -->
                    <div class="flex text-[#F5C518]">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 {{ $i <= $rev->rating ? 'fill-current' : 'text-gray-200' }}" viewBox="0 0 24 24">
                                <path d="M12 2.5l2.9 6.1 6.6.8-4.9 4.6 1.3 6.6L12 17.3l-5.9 3.3 1.3-6.6L2.5 9.4l6.6-.8z" />
                            </svg>
                        @endfor
                    </div>
                </div>
                <p class="text-sm text-gray-700 leading-relaxed pl-10 whitespace-pre-line">{{ $rev->comment }}</p>
            </div>
        @empty
            <div class="text-center py-8 text-gray-400 text-sm">
                {{ __('No reviews yet. Be the first to review this product!') }}
            </div>
        @endforelse
    </div>

</div>