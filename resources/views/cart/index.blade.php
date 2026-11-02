<x-layouts::shop :title="__('Shopping Cart')">
    {{-- ตัวแปรจาก CartController@index: $cart, $itemCount, $subtotal, $shipping, $total --}}

    <!-- Breadcrumbs -->
    <div class="breadcrumbs text-sm text-gray-500 p-0 mb-4">
        <ul>
            <li><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
            <li class="text-gray-700 font-medium">{{ __('Cart') }}</li>
        </ul>
    </div>

    <!-- Header Title & Item Count -->
    <div class="flex items-baseline justify-between mb-8">
        <h1 class="text-3xl md:text-4xl font-extrabold text-[#090F3B]">{{ __('Shopping Cart') }}</h1>
        <span class="text-sm text-[#5A6BD5] font-medium" data-cart="item-label">{{ $itemCount }} {{ Str::plural('item', $itemCount) }}</span>
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
            
            <!-- ตารางรายการสินค้า (ยาวได้ไม่จำกัด กล่องสรุปด้านขวาจะเลื่อนตาม) -->
            <div class="min-w-0 bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-6 md:p-8">
                <!-- Table Header -->
                <div class="hidden sm:grid grid-cols-[minmax(0,1fr)_90px_120px_100px] gap-x-3 text-xs font-semibold text-[#5A6BD5] uppercase tracking-wider pb-4 border-b border-gray-100">
                    <div>{{ __('Product') }}</div>
                    <div class="text-center">{{ __('Price') }}</div>
                    <div class="text-center">{{ __('Quantity') }}</div>
                    <div class="text-right">{{ __('Total') }}</div>
                </div>

                <!-- รายการแถวสินค้า -->
                <div class="divide-y divide-gray-100">
                    @foreach ($cart as $id => $item)
                        <div class="grid grid-cols-[minmax(0,1fr)_auto] sm:grid-cols-[minmax(0,1fr)_90px_120px_100px] gap-x-3 gap-y-3 items-center py-5 transition-opacity" data-cart-row="{{ $id }}">
                            
                            <!-- สินค้า & รูป -->
                            <div class="col-span-2 sm:col-span-1 flex items-center gap-4 min-w-0">
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
                                    <h3 class="font-bold text-[#090F3B] line-clamp-2 break-words text-sm md:text-base leading-snug" title="{{ $item['name'] }}">{{ $item['name'] }}</h3>
                                    <p class="sm:hidden text-xs text-gray-500 tabular-nums">{{ number_format($item['price'], 2) }} {{ __('each') }}</p>
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
                            <div class="hidden sm:block text-center text-sm font-semibold text-gray-700 tabular-nums">
                                {{ number_format($item['price'], 2) }}
                            </div>

                            <!-- ปรับจำนวน − (qty) + : ส่งแบบ fetch (ไม่รีโหลด) ถ้า JS ปิดอยู่จะส่งฟอร์มปกติแทน -->
                            <div class="flex items-center justify-start sm:justify-center gap-1">
                                <form method="POST" action="{{ route('cart.update', $id) }}" data-cart-qty="dec">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}">
                                    <button type="submit" aria-label="{{ __('Decrease quantity') }}" @disabled($item['quantity'] <= 1)
                                            class="w-7 h-7 rounded-full grid place-items-center text-[#5A6BD5] hover:bg-[#EEF0FB] hover:text-[#090F3B] disabled:opacity-30 disabled:hover:bg-transparent text-base font-bold transition">−</button>
                                </form>

                                <span class="w-8 h-7 rounded-full border border-[#B9C2FF] text-[#090F3B] text-xs font-semibold grid place-items-center tabular-nums" data-cart="qty" aria-live="polite">
                                    {{ $item['quantity'] }}
                                </span>

                                <form method="POST" action="{{ route('cart.update', $id) }}" data-cart-qty="inc">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                    <button type="submit" aria-label="{{ __('Increase quantity') }}" @disabled($item['quantity'] >= ($item['stock'] ?? PHP_INT_MAX))
                                            class="w-7 h-7 rounded-full grid place-items-center text-[#5A6BD5] hover:bg-[#EEF0FB] hover:text-[#090F3B] disabled:opacity-30 disabled:hover:bg-transparent text-base font-bold transition">+</button>
                                </form>
                            </div>

                            <!-- ราคารวมของแถวนั้น (สีแดงตาม Figma) -->
                            <div class="text-right font-bold text-[#E5484D] text-sm md:text-base tabular-nums" data-cart="line-total">
                                {{ number_format($item['price'] * $item['quantity'], 2) }}
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <!-- กล่อง Order Summary ด้านขวา (sticky: เลื่อนดูสินค้าเยอะ ๆ แล้วปุ่ม Checkout ยังอยู่ในจอ) -->
            <div class="lg:sticky lg:top-28 bg-white rounded-2xl shadow-[0_4px_0_0_#3B4BB0,0_8px_24px_-8px_rgba(9,15,59,0.25)] p-6 md:p-7">
                <h2 class="text-xl font-bold text-[#090F3B] mb-5">{{ __('Order Summary') }}</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-[#5A6BD5]">
                        <span>{{ __('Subtotal') }} (<span data-cart="item-label">{{ $itemCount }} {{ Str::plural('item', $itemCount) }}</span>)</span>
                        <span class="font-semibold text-gray-800 tabular-nums" data-cart="subtotal">{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-[#5A6BD5]">
                        <span>{{ __('Shipping') }}</span>
                        <span class="font-semibold text-gray-800">{{ $shipping == 0 ? __('Free') : number_format($shipping, 2) }}</span>
                    </div>
                </div>

                <div class="my-5 border-t border-gray-100"></div>

                <div class="flex items-baseline justify-between mb-3">
                    <span class="font-bold text-[#090F3B]">{{ __('Total') }}</span>
                    <span class="text-2xl font-black text-[#E5484D] tabular-nums" data-cart="total">{{ number_format($total, 2) }}</span>
                </div>

                <p class="text-xs text-[#5A6BD5] mb-6">
                    {{ __('Have a coupon? Apply it at checkout.') }}
                </p>

                <a href="{{ route('checkout.index') }}" class="btn w-full rounded-xl border-none bg-[#090F3B] hover:bg-[#1E2A6E] text-white font-semibold py-3 shadow-md">
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

    @if (! empty($cart))
        <script>
            // ปุ่ม + / − : ส่งไป cart.update แบบ fetch แล้วอัปเดตตัวเลขในหน้า (จำนวน, ราคาแถว, ยอดรวม, เลขบนไอคอนตะกร้า)
            document.querySelectorAll('form[data-cart-qty]').forEach((form) => {
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    const row = form.closest('[data-cart-row]');
                    if (row.dataset.busy) return;
                    row.dataset.busy = '1';
                    row.classList.add('opacity-60');

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: new FormData(form),
                        });
                        const data = await response.json().catch(() => ({}));
                        if (! response.ok) throw new Error(data.message || 'Could not update the cart.');

                        row.querySelector('[data-cart="qty"]').textContent = data.quantity;
                        row.querySelector('[data-cart="line-total"]').textContent = data.line_total;
                        row.querySelector('form[data-cart-qty="dec"] input[name="quantity"]').value = Math.max(1, data.quantity - 1);
                        row.querySelector('form[data-cart-qty="inc"] input[name="quantity"]').value = data.quantity + 1;
                        row.querySelector('form[data-cart-qty="dec"] button').disabled = data.quantity <= 1;
                        row.querySelector('form[data-cart-qty="inc"] button').disabled = data.quantity >= data.stock;

                        document.querySelectorAll('[data-cart="item-label"]').forEach((el) => el.textContent = data.item_label);
                        document.querySelector('[data-cart="subtotal"]').textContent = data.subtotal;
                        document.querySelector('[data-cart="total"]').textContent = data.total;
                        const badge = document.querySelector('[data-test="cart-count"]');
                        if (badge) badge.textContent = data.item_count > 99 ? '99+' : data.item_count;
                    } catch (error) {
                        showCartToast(error.message);
                    } finally {
                        delete row.dataset.busy;
                        row.classList.remove('opacity-60');
                    }
                });
            });

            // แจ้งเตือนสีแดงมุมขวาบน (หน้าตาเดียวกับ flash toast ใน layout)
            function showCartToast(message) {
                document.getElementById('cart-toast')?.remove();
                const toast = document.createElement('div');
                toast.id = 'cart-toast';
                toast.setAttribute('role', 'alert');
                toast.className = 'fixed top-24 right-4 left-4 sm:left-auto z-50 sm:max-w-sm transition-all duration-300';
                toast.innerHTML = '<div class="flex items-center gap-3 rounded-xl border px-4 py-3 shadow-lg bg-[#FEF3F2] border-[#FECDCA] text-[#B42318]"><span class="flex-1 font-medium"></span></div>';
                toast.querySelector('span').textContent = message;
                document.body.appendChild(toast);
                setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
            }
        </script>
    @endif
</x-layouts::shop>
