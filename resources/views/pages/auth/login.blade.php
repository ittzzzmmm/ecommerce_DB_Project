<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

    

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

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
            <div class="relative">
              <label for="password" class="block font-medium mb-1">{{ __('Password') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="{{ __('password') }}"
                    class="input w-full rounded-lg @error('password') input-error @enderror"
                />
                @error('password')
                    <p class="text-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

           <!-- Remember Me -->
<label class="flex items-center gap-2 text-sm cursor-pointer">
    <input type="checkbox" name="remember" class="checkbox checkbox-primary checkbox-sm" @checked(old('remember'))>
    {{ __('Remember me') }}
</label>

           <div class="flex items-center justify-end mt-4">
    <button type="submit" class="btn btn-primary btn-block rounded-lg font-[Poppins] font-semibold" data-test="login-button">
        {{ __('Login') }}
    </button>
</div>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-sm font-[Poppins]">
            <span>{{ __('Don\'t have an account?') }}</span>
              <a href="{{ route('register') }}" class="link link-primary link-hover" wire:navigate>{{ __('Sign up') }}</a>
        </div>
    </div>
</x-layouts::auth>
