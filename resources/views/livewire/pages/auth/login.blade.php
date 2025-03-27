<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\ActivityLog;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

       

        $this->form->authenticate();

        Session::regenerate();
 
        $user = Auth::user();
 
        $agent = new \Jenssegers\Agent\Agent();

        $data = [
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'browser' => $agent->browser(),
            'device' => $agent->device(),
            'platform' => $agent->platform(),
            'user_agent' => request()->header('User-Agent'),
            'location' => request()->header('X-Forwarded-For') ?? request()->ip(),
        ];

        // Check if the device is already logged
        $deviceLog = \App\Models\UserDeviceLog::firstOrCreate($data);
 

        // ✅ **If device is trusted, store 2FA verification in session**
        if ($deviceLog->trusted) {
            session()->put('2fa_verified', true);
        } 



        if (Auth::check() && !session()->has('2fa_verified')) {
            // Generate a 6-digit OTP
            $otp = rand(100000, 999999);

            // Save OTP to user table
            $user->update([
                'otp_code' => $otp,
                'otp_expires_at' => \Carbon\Carbon::now()->addMinutes(5), // OTP expires in 5 minutes
            ]);

            // Send OTP via email
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TwoFactorMail($otp));
            
            // return redirect()->route('2fa.verify'); 
            $this->redirectIntended(default: route('2fa.verify', absolute: false));


        }else{

            // return redirect()->route('dashboard'); 
            $this->redirectIntended(default: route('dashboard', absolute: false));
        }
 
 
        

        // $this->redirectIntended(default: route('dashboard', absolute: false));
    }
}; ?>

<div>

    {{-- <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div> --}}

    <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>


        </div>

        <div class="flex items-center justify-center mt-8">
            <a class=" text-sm text-indigo-500 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}" wire:navigate>
                {{ __('Not registered yet? Signup here') }}
            </a>
        </div>
    </form>
</div>
