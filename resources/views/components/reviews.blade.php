@php
    $myReview = auth()->check()
        ? $product->reviews->firstWhere('user_id', auth()->id())
        : null;
@endphp

<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(280px,0.7fr)]">
    <div class="rounded-xl bg-white p-6 shadow-sm md:p-8">
        <h2 class="text-xl font-bold text-[#090F3B]">{{ __('Customer reviews') }}</h2>

        @forelse ($product->reviews->sortByDesc('created_at') as $review)
            <article class="border-b border-gray-100 py-5 last:border-b-0">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-semibold text-[#090F3B]">{{ $review->user->name }}</h3>
                    <span class="text-sm text-amber-600" aria-label="{{ __('Rating: :rating out of 5', ['rating' => $review->rating]) }}">
                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                    </span>
                </div>
                @if ($review->comment)
                    <p class="mt-2 whitespace-pre-line text-gray-700">{{ $review->comment }}</p>
                @endif
                <p class="mt-2 text-xs text-gray-500">{{ $review->created_at->format('M j, Y') }}</p>
            </article>
        @empty
            <p class="mt-4 text-gray-600">{{ __('No reviews yet. Be the first to review this product.') }}</p>
        @endforelse
    </div>

    <div class="h-fit rounded-xl bg-white p-6 shadow-sm md:p-8">
        <h2 class="text-xl font-bold text-[#090F3B]">{{ $myReview ? __('Update your review') : __('Write a review') }}</h2>

        @auth
            <form method="POST"
                  action="{{ $myReview ? route('reviews.update', $myReview) : route('products.reviews.store', $product) }}"
                  class="mt-5 space-y-4">
                @csrf
                @if ($myReview)
                    @method('PUT')
                @endif

                <div>
                    <label for="review-rating" class="mb-1 block text-sm font-medium">{{ __('Rating') }}</label>
                    <select id="review-rating" name="rating" required class="select select-bordered w-full">
                        @for ($ratingOption = 5; $ratingOption >= 1; $ratingOption--)
                            <option value="{{ $ratingOption }}" @selected((int) old('rating', $myReview?->rating ?? 5) === $ratingOption)>
                                {{ $ratingOption }} / 5
                            </option>
                        @endfor
                    </select>
                    @error('rating')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="review-comment" class="mb-1 block text-sm font-medium">{{ __('Comment') }}</label>
                    <textarea id="review-comment" name="comment" rows="4" maxlength="5000"
                              class="textarea textarea-bordered w-full">{{ old('comment', $myReview?->comment) }}</textarea>
                    @error('comment')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn w-full rounded-lg bg-[#090F3B] text-white hover:bg-[#1E2A6E]">
                    {{ $myReview ? __('Save review') : __('Submit review') }}
                </button>
            </form>

            @if ($myReview)
                <form method="POST" action="{{ route('reviews.destroy', $myReview) }}" class="mt-3"
                      onsubmit="return confirm('{{ __('Remove your review?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost w-full text-error">{{ __('Delete review') }}</button>
                </form>
            @endif
        @else
            <p class="mt-3 text-gray-600">{{ __('Log in to write a review.') }}</p>
            <a href="{{ route('login') }}" class="btn mt-4 rounded-lg bg-[#090F3B] text-white">{{ __('Log in') }}</a>
        @endauth
    </div>
</div>
