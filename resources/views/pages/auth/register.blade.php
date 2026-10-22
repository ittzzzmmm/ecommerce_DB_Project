<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-4">
        <!-- Header -->
        <h1 class="text-3xl md:text-4xl font-bold text-primary mb-2">{{ __('Welcome to CPE10') }}</h1>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

       <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-3">
            @csrf
            <!-- Name -->
            <div>
                <label for="name" class="block font-medium mb-1">{{ __('Name') }}</label>
                <input
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="{{ __('Enter your name') }}"
                    class="input w-full rounded-lg @error('name') input-error @enderror"
                />
                @error('name')
                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block font-medium mb-1">{{ __('Email') }}</label>
                <input
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="{{ __('Enter your email') }}"
                    class="input w-full rounded-lg @error('email') input-error @enderror"
                />
                @error('email')
                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block font-medium mb-1">{{ __('Password') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('password') }}"
                    passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                    class="input w-full rounded-lg @error('password') input-error @enderror"
                />
                @error('password')
                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password (ไม่มีใน Figma แต่ backend บังคับด้วยกฎ 'confirmed' ห้ามลบ) -->
            <div>
                <label for="password_confirmation" class="block font-medium mb-1">{{ __('Confirm password') }}</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('confirm password') }}"
                    passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                    class="input w-full rounded-lg"
                />
            </div>

          <div class="flex items-center justify-end mt-4">
                <button type="submit" class="btn btn-primary btn-block rounded-lg font-[Poppins] font-semibold" data-test="register-user-button">
                    {{ __('Sign up') }}
                </button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm font-[Poppins]">
            <span>{{ __('Have an account?') }}</span>
            <a href="{{ route('login') }}" class="link link-primary link-hover" wire:navigate>{{ __('Login') }}</a>
        </div>
    </div>
</x-layouts::auth>