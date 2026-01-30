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

        //    dd("Okay");

        $this->form->authenticate();

        // dd("After Auth");

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
        
        // dd($deviceLog);

        // ✅ **If device is trusted, store 2FA verification in session**
        if ($deviceLog->trusted) {
            session()->put('2fa_verified', true);
        } 

        // If the user has the role Admin or DSI God Admin, skip 2FA
        if ($user->can('system access admin') ) {
            session()->put('2fa_verified', true);
        }

        // If the user has the role Admin or DSI God Admin, skip 2FA
        if ($user->can('system access global admin') ) {
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

<div  >

    {{-- <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div> --}}

    {{-- <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div> --}}

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        {{-- <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div> --}}

        <!-- Password -->
        <div class="mt-4" x-data="{ show: false }">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input
                    wire:model="form.password"
                    x-bind:type="show ? 'text' : 'password'"
                    id="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    class="block mt-1 w-full pr-10"
                />

                <!-- Toggle Button -->
                <button type="button"
                        x-on:click="show = !show"
                        class="absolute inset-y-0 right-2 flex items-center text-gray-600 hover:text-gray-800"
                        tabindex="-1">
                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.045 10.045 0 014.724-5.735M6.182 6.182l11.636 11.636M17.818 17.818L6.182 6.182"/>
                    </svg>
                </button>
            </div>

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


    <!-- Fullscreen Cen ered Loader with Dark Background -->
    <div wire:loading  wire:target="login"
     
     >
        <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
            <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                </svg>
                <div class="text-sm font-medium">
                    Logging in, please wait...
                </div>
            </div>
        </div>

        
    </div>






</div>
