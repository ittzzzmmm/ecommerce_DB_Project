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

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

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

<div class="flex flex-col md:flex-row gap-6">
    <!-- เมนูด้านซ้าย -->
    <aside class="md:w-56 shrink-0">
        <div class="flex items-center gap-3 pb-4 mb-4 border-b border-base-300">
            <div class="avatar avatar-placeholder">
                <div class="w-12 rounded-full bg-primary text-primary-content">
                    <span class="font-bold">{{ auth()->user()->initials() }}</span>
                </div>
            </div>
            <div class="min-w-0">
                <p class="font-medium truncate">{{ auth()->user()->name }}</p>
                <a href="{{ route('profile.edit') }}" class="text-sm text-base-content/60 hover:text-primary" wire:navigate>{{ __('Edit Profile') }}</a>
            </div>
        </div>

        <ul class="menu w-full p-0 font-[Poppins] text-sm">
            <li class="menu-title text-black">{{ __('My Account') }}</li>
            <li><a href="{{ route('profile.edit') }}" class="bg-primary/10 text-primary font-semibold" wire:navigate>{{ __('Profile') }}</a></li>
            <li><a href="{{ route('security.edit') }}" wire:navigate>{{ __('Change Password') }}</a></li>

            <li class="menu-title text-black mt-2">{{ __('My Purchase') }}</li>
            <li class="menu-disabled"><span>{{ __('Coming soon') }}</span></li>
        </ul>
    </aside>

    <!-- การ์ดข้อมูลโปรไฟล์ -->
    <section class="flex-1 card bg-white shadow-sm">
        <div class="card-body p-6 md:p-8">
            <div class="border-b border-base-300 pb-4 mb-6">
                <h1 class="text-xl font-medium">{{ __('My Profile') }}</h1>
                <p class="text-sm text-base-content/60">{{ __('Manage and protect your account') }}</p>
            </div>

            <div class="flex flex-col-reverse lg:flex-row gap-8">
                <!-- ฟอร์ม -->
                <form wire:submit="updateProfileInformation" class="flex-1 flex flex-col gap-5">
                    <!-- Name -->
                    <div class="grid md:grid-cols-[8rem_1fr] md:items-center gap-1 md:gap-4">
                        <label for="name" class="md:text-right text-base-content/70">{{ __('Name') }}</label>
                        <div>
                            <input
                                id="name"
                                wire:model="name"
                                type="text"
                                required
                                autofocus
                                autocomplete="name"
                                class="input w-full rounded-lg @error('name') input-error @enderror"
                            />
                            @error('name')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="grid md:grid-cols-[8rem_1fr] gap-1 md:gap-4">
                        <label for="email" class="md:text-right md:pt-2 text-base-content/70">{{ __('Email') }}</label>
                        <div>
                            <input
                                id="email"
                                wire:model="email"
                                type="email"
                                required
                                autocomplete="email"
                                class="input w-full rounded-lg @error('email') input-error @enderror"
                            />
                            @error('email')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror

                            <!-- สถานะยืนยันอีเมล -->
                            @if ($this->hasUnverifiedEmail)
                                <div class="mt-2 text-sm">
                                    <span class="badge badge-warning badge-sm">{{ __('Unverified') }}</span>
                                    <button type="button" class="link link-primary ml-1" wire:click.prevent="resendVerificationNotification">
                                        {{ __('Re-send verification email') }}
                                    </button>

                                    @if (session('status') === 'verification-link-sent')
                                        <p class="text-success mt-1">{{ __('A new verification link has been sent to your email address.') }}</p>
                                    @endif
                                </div>
                            @else
                                <span class="badge badge-success badge-sm mt-2">{{ __('Verified') }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Member since (แสดงอย่างเดียว แก้ไม่ได้) -->
                    <div class="grid md:grid-cols-[8rem_1fr] gap-1 md:gap-4">
                        <span class="md:text-right text-base-content/70">{{ __('Member since') }}</span>
                        <span>{{ auth()->user()->created_at?->format('d M Y') }}</span>
                    </div>

                    <!-- Save -->
                    <div class="grid md:grid-cols-[8rem_1fr] gap-4">
                        <div class="hidden md:block"></div>
                        <div>
                            <button type="submit" class="btn btn-primary rounded-lg px-8 font-[Poppins] font-semibold" data-test="update-profile-button" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="updateProfileInformation">{{ __('Save') }}</span>
                                <span wire:loading wire:target="updateProfileInformation" class="loading loading-spinner loading-sm"></span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- รูปโปรไฟล์ (ตอนนี้แสดงตัวอักษรย่อ เพราะยังไม่มีระบบอัปโหลดรูป) -->
                <div class="lg:w-56 lg:border-l border-base-300 lg:pl-8 flex flex-col items-center gap-3">
                    <div class="avatar avatar-placeholder">
                        <div class="w-24 rounded-full bg-primary text-primary-content">
                            <span class="text-3xl font-bold">{{ auth()->user()->initials() }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-base-content/50 text-center">{{ __('Profile picture coming soon') }}</p>
                </div>
            </div>
        </div>
    </section>
</div>