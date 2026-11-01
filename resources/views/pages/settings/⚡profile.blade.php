<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::shop')] #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';
    public string $address = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->address = Auth::user()->address ?? '';
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            ...$this->profileRules($user->id),
            // ที่อยู่เริ่มต้น (ใช้เติมให้อัตโนมัติตอน Checkout) ไม่บังคับ
            'address' => ['nullable', 'string', 'max:2000'],
        ]);
        $validated['address'] = filled($validated['address'] ?? null) ? trim($validated['address']) : null;

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
};
?>
<div class="flex flex-col lg:flex-row gap-8 lg:gap-8">
    <!-- เมนูด้านซ้าย -->
    <aside class="lg:w-72 shrink-0">
        <div class="flex items-center gap-5 pb-4 mb-6 border-b border-base-300">
            <div class="avatar avatar-placeholder">
                <div class="w-24 rounded-full bg-[#090F3B] text-white">
                    <span class="text-2xl font-bold">{{ auth()->user()->initials() }}</span>
                </div>
            </div>
            <div class="min-w-0">
                <p class="text-xl font-medium truncate">{{ auth()->user()->name }}</p>
                <a href="{{ route('profile.edit') }}" class="text-primary hover:underline" wire:navigate>{{ __('edit profile') }}</a>
            </div>
        </div>

        <nav class="flex flex-col gap-1 px-1">
            <p class="font-medium">{{ __('My Account') }}</p>
            <a href="{{ route('profile.edit') }}" class="pl-1 text-primary font-semibold" wire:navigate>{{ __('profile') }}</a>
            <a href="{{ route('security.edit') }}" class="pl-1 text-primary hover:underline" wire:navigate>{{ __('change password') }}</a>

            <p class="font-medium mt-4">{{ __('My Purchase') }}</p>
            <a href="{{ route('orders.index') }}" class="pl-1 text-primary hover:underline" data-test="my-purchase-link">{{ __('order history') }}</a>
        </nav>
    </aside>

    <!-- การ์ดข้อมูลโปรไฟล์ (เงาสีม่วงด้านล่างตาม Figma) -->
    <section class="flex-1 bg-white rounded-md border border-base-200 shadow-[0_4px_4px_rgba(30,42,110,0.6)] min-h-[40rem]">
        <div class="p-6 md:p-8 flex flex-col-reverse lg:flex-row gap-10">
            <div class="flex-1">
                <h1 class="text-4xl font-bold">{{ __('My Profile') }}</h1>
                <p class="text-primary mt-1">{{ __('manage and protect your account') }}</p>

                <form wire:submit="updateProfileInformation" class="mt-12 flex flex-col gap-4 max-w-xl px-1 md:px-8">
                    <!-- Name -->
                    <div class="grid sm:grid-cols-[5.5rem_1fr] sm:items-center gap-1 sm:gap-3">
                        <label for="name" class="font-medium">{{ __('Name') }}</label>
                        <div>
                            <input
                                id="name"
                                wire:model="name"
                                type="text"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="{{ __('Enter your name') }}"
                                class="input input-sm h-8 w-full rounded-md @error('name') input-error @enderror"
                            />
                            @error('name')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="grid sm:grid-cols-[5.5rem_1fr] gap-1 sm:gap-3">
                        <label for="email" class="font-medium sm:pt-1">{{ __('Email') }}</label>
                        <div>
                            <input
                                id="email"
                                wire:model="email"
                                type="email"
                                required
                                autocomplete="email"
                                placeholder="{{ __('Enter your email') }}"
                                class="input input-sm h-8 w-full rounded-md @error('email') input-error @enderror"
                            />
                            @error('email')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror

                            <!-- สถานะยืนยันอีเมล -->
                            @if ($this->hasUnverifiedEmail)
                                <div class="mt-1 text-xs flex flex-wrap gap-x-4">
                                    <span class="text-error">{{ __('unverified') }}</span>
                                    <button type="button" class="text-primary hover:underline" wire:click.prevent="resendVerificationNotification">
                                        {{ __('Re-send verification email') }}
                                    </button>
                                </div>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="text-success text-xs mt-1">{{ __('A new verification link has been sent to your email address.') }}</p>
                                @endif
                            @else
                                <p class="mt-1 text-xs text-success">{{ __('verified') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Address: ที่อยู่เริ่มต้นสำหรับจัดส่ง/ออกบิล (users.address) -->
                    <div class="grid sm:grid-cols-[5.5rem_1fr] gap-1 sm:gap-3">
                        <label for="address" class="font-medium sm:pt-1">{{ __('Address') }}</label>
                        <div>
                            <textarea
                                id="address"
                                wire:model="address"
                                rows="3"
                                autocomplete="street-address"
                                placeholder="{{ __('House no, street, district, province, postal code, phone') }}"
                                class="textarea w-full rounded-md text-sm leading-relaxed @error('address') textarea-error @enderror"
                                data-test="profile-address"
                            ></textarea>
                            @error('address')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">{{ __('Used for shipping and billing. It will be filled in for you at checkout.') }}</p>
                        </div>
                    </div>

                    <!-- ข้อมูลแสดงอย่างเดียว (แก้ไม่ได้) -->
                    <div class="mt-6 grid grid-cols-[9rem_1fr] items-center gap-y-4">
                        <span class="font-medium">{{ __('Payment Details') }}</span>
                        <span class="text-xs text-primary">{{ auth()->user()->payment_details ?? '-' }}</span>


                        <span class="font-medium">{{ __('Member since') }}</span>
                        <span class="text-sm text-primary">{{ auth()->user()->created_at?->format('d M Y') }}</span>
                    </div>

                    <!-- Save -->
                    <div class="mt-2">
                        <button type="submit" class="btn btn-sm h-9 rounded-xl border-none bg-[#1E2A6E] hover:bg-[#090F3B] text-white px-6 font-semibold" data-test="update-profile-button" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updateProfileInformation">{{ __('Save') }}</span>
                            <span wire:loading wire:target="updateProfileInformation" class="loading loading-spinner loading-sm"></span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- รูปโปรไฟล์ (ตอนนี้แสดงตัวอักษรย่อ เพราะยังไม่มีระบบอัปโหลดรูป) -->
            <div class="lg:w-56 lg:pt-6 flex flex-col items-center gap-4">
                <div class="avatar avatar-placeholder">
                    <div class="w-44 rounded-full bg-[#090F3B] text-white">
                        <span class="text-5xl font-bold">{{ auth()->user()->initials() }}</span>
                    </div>
                </div>
                <button type="button" class="text-primary hover:underline" disabled>{{ __('edit profile picture') }}</button>
            </div>
        </div>
    </section>
</div>
