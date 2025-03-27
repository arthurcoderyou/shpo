 
<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\ActivityLog;
use App\Livewire\Actions\Logout;

new #[Layout('layouts.guest')] class extends Component
{
    // public LoginForm $form;
 

    /**
     * Handle an incoming authentication request.
     */
    // public function login(): void
    // {
    //     $this->validate();

       

    //     $this->form->authenticate();

    //     Session::regenerate();
 
    //     $user = Auth::user();

    //     // Generate a 6-digit OTP
    //     $otp = rand(100000, 999999);

    //     // Save OTP to user table
    //     $user->update([
    //         'otp_code' => $otp,
    //         'otp_expires_at' => \Carbon\Carbon::now()->addMinutes(5), // OTP expires in 5 minutes
    //     ]);

    //     // Send OTP via email
    //     Mail::to($user->email)->send(new \App\Mail\TwoFactorMail($otp));

    //     // Redirect to OTP verification page
    //     // return redirect()->route('2fa.verify'); 
    //     $this->redirectIntended(default: route('2fa.verify', absolute: false));
        

    //     // $this->redirectIntended(default: route('dashboard', absolute: false));
    // }



    public $otp_code;

    public function mount(){
        // dd(Auth::user());

        if (session()->has('2fa_verified') && session('2fa_verified') === true) {
            return redirect()->route('dashboard');
        }


        $this->user = Auth::user();
 

    }


    public function resend_otp_code()
    {
        $user = Auth::user();

        if (!$user) {
            session()->flash('error', 'User not authenticated.');
            return;
        }

        // Check if an existing OTP is still valid
        if ($user->otp_code && \Carbon\Carbon::now()->lt($user->otp_expires_at)) {
            $otp = $user->otp_code;
        } else {
            // Generate a new 6-digit OTP
            $otp = rand(100000, 999999);

            // Save OTP to the user's table with a new expiration time
            $user->update([
                'otp_code' => $otp,
                'otp_expires_at' => \Carbon\Carbon::now()->addMinutes(5), // OTP expires in 5 minutes
            ]);
        }


        $mail_user = \App\Models\User::where('id',$user->id)->first();

        // Send OTP via email
        Mail::to($user->email)->send(new \App\Mail\TwoFactorMail($otp));




        // Flash success message
        session()->flash('status', 'A new OTP code has been sent to your email.');
    }



    public function verify(){
        
        $this->validate([
            'otp_code' => 'required|min:6',
        ]);

        $user = Auth::user();

        if ($user->otp_code == $this->otp_code && \Carbon\Carbon::now()->lt($user->otp_expires_at)) {
            session()->put('2fa_verified', true);

            // Clear OTP after successful verification
            $user->update([
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);

            return redirect()->route('dashboard');
        }

        session()->flash('error', 'Invalid or expired OTP.');
        $this->addError('otp_code','Invalid or expired OTP.');


        return;

    }


     /**
     * Log the current user out of the application.
     */
     public function logout(Logout $logout): void
    {

        // Destroy session
        Session::flush();
        
        $logout();

        $this->redirect('/', navigate: true);
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

    <form wire:submit="verify">
        <div class="  text-sm text-gray-600">
            {{ __('Two-Factor Authentication ') }}
        </div>
    
        

        <div class=" ">
             
            <label for="otp_code" class="inline-block text-sm font-medium text-gray-800   dark:text-neutral-200">
                Enter the 6-digit code sent to your email.
            </label>

            <input
            autofocus autocomplete="otp_code"
            wire:model="otp_code"
            id="otp_code" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">

            @error('otp_code')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror


        </div>

        <div class="flex items-center justify-end mt-4">
            

            <x-primary-button class="ms-3" wire:click="verify">
                {{ __('Verify') }}
            </x-primary-button>


        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="  font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <button wire:click="resend_otp_code" type="button"  class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"  >
                {{ __('Resend OTP Code?') }}
            </button>
    
            <button type="button" wire:click="logout"  class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </div>

         
    </form>
</div>
