<?php

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Notifications\NewUserRegisteredNotification;
use App\Services\CustomEncryptor;


new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $address = '';
    public string $company = '';
    public string $phone_number = '';

    public string $backup; // encrypted password 


    public string $password = '';
    public string $password_confirmation = '';
    public string $role_request = 'user'; 

    public array $companies = [];
    
    /**
     * Returns default information 
     * @return void         void           Not required to return value
     */
    public function mount(){


        $role_request = request('role');
        
        
        if($role_request != 'reviewer'){
            $role_request = 'user';
        }

        $this->role_request = $role_request;



        $limit = (int) 20;
        $this->companies = User::query()
            ->whereNotNull('company')
            ->where('company', '!=', '')
            ->select('company')
            ->groupBy('company')
            ->orderBy('company')
            ->limit($limit)
            ->pluck('company')
            ->toArray();

            

    }

    /**
     * Updated company
     * @return void         void           Not required to return value
     */
    public function updatedCompany(){

        $q = $this->company;
        $limit = (int) 20;

        // $limit = max(5, min($limit, 50)); // safety clamp

        $query = User::query()
            ->select('company')
            ->whereNotNull('company')
            ->where('company', '!=', '')
            ->groupBy('company')
            ->orderBy('company');

        if ($this->company !== '') {
            // For index usage, prefix search is better:
            $query = $query->where('company', 'like', $q . '%');
            // If you prefer contains: '%' . $q . '%', but index benefit is less.
        }
 

        $this->companies = $query->limit($limit)->pluck('company')->toArray();

        
    }



    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {

        // dd($this->all());

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'address' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255'], 
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role_request' => ['required']
        ]);

        // Determine role_request automatically based on email
        // $email = strtolower($validated['email']);
        // $role_request = User::getRoleRequestByEmail($email);

        // using the selected request 
        $role_request = $validated['role_request'];

        

        $validated['role_request'] = $role_request;
        $crypt = app(CustomEncryptor::class);
        $encrypted = $crypt->encrypt($validated['password']);
        
        $validated['password'] = Hash::make($validated['password']);
 
        $validated['backup'] = $encrypted;

        event(new Registered($user = User::create($validated)));


        // ActivityLog::create([
        //     'log_action' => "New user \"".$user->name."\" has registered.",
        //     'log_username' => $user->name,
        //     'created_by' => $user->id,
        // ]);


        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false));
    }
}; ?>

<div >

    {{-- <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div> --}}

    <form wire:submit="register">
        <div class=" rounded-xl bg-white shadow-sm ring-1 ring-gray-200 p-6">
            <fieldset>
                <legend class="text-base font-semibold text-gray-900">What will you use this account for?</legend>
                <p class="mt-1 text-sm text-gray-600">Select the role that matches your purpose for using the site.</p>

                <div class="mt-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <input wire:model.live="role_request" id="role_user" name="role_request" type="radio" value="user"
                            class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-600"
                        >
                        <label for="role_user" class="block text-sm font-medium text-gray-900">
                            <span class="font-semibold">Submitter</span> — Create and manage projects
                        </label>
                    </div>

                    <div class="flex items-center gap-3">
                        <input wire:model="role_request" id="role_reviewer" name="role_request" type="radio" value="reviewer"
                            class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-600"
                        >
                        <label for="role_reviewer" class="block text-sm font-medium text-gray-900">
                            <span class="font-semibold">Reviewer</span> — Review submitted projects
                        </label>
                    </div>
                </div>

                <x-input-error :messages="$errors->get('role_request')" class="mt-2" />
            </fieldset>
        </div>




        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            {{-- <x-input-label for="company" :value="__('Company')" />
            <x-text-input wire:model="company" id="company" name="company" type="text" class="mt-1 block w-full" required   autocomplete="company" />
            <x-input-error class="mt-2" :messages="$errors->get('company')" /> --}}

             <x-inputs.text-dropdown
                name="company"
                label="Company"
                :value="$company"
                {{-- placeholder="Search or select company..." --}}
                :options="$companies"    
                wire:model.live="company"       
            />

            <x-input-error class="mt-2" :messages="$errors->get('company')" />

        </div>


        <!-- Address -->
        <div class="mt-4">
            <x-input-label for="address" :value="__('Address')" />
            <x-text-input wire:model="address" id="address" class="block mt-1 w-full" type="text" name="address" required autocomplete="address" />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>


        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone_number" :value="__('Phone number')" />
            <x-text-input wire:model="phone_number" id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" required autocomplete="phone_number" />
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>



        <!-- Password -->
        <div class="mt-4" x-data="{ show: false }">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input
                    wire:model="password"
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

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

      
        <!-- Confirm Password -->
        <div class="mt-4" x-data="{ show: false }">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div class="relative">
                <x-text-input
                    wire:model="password_confirmation"
                    x-bind:type="show ? 'text' : 'password'"
                    id="password_confirmation"
                    name="password_confirmation"
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

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>


        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>


            @if(request('role_request') == 'reviewer')
                <x-primary-button class="ms-4">
                    {{ __('Register as Reviewer') }}
                </x-primary-button>
            @else
                <x-primary-button class="ms-4">
                    {{ __('Register') }}
                </x-primary-button>
            @endif 
        </div>
    </form>
 

    <!-- Loaders -->
        {{-- wire:target="register"   --}}
        <div wire:loading  wire:target="register"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                        Registering your account, please wait...
                    </div>
                </div>
            </div>

            
        </div>
    <!-- ./ Loaders -->

</div>
