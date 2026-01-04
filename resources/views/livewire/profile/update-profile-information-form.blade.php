<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use App\Models\ActivityLog;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $address = '';
    public string $company = '';
    public string $phone_number = '';

   public array $companies = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->address = Auth::user()->address ?? '';
        $this->company = Auth::user()->company ?? '';
        $this->phone_number = Auth::user()->phone_number ?? '';

 
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
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation() // : void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'address' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);

        ActivityLog::create([
            'log_action' => "User \"".Auth::user()->name."\" has updated their profile information.",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        return redirect()->route('profile')->with('alert.success',"Profile updated successfully.",);
        


    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }


     
    
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        
        <div>
            {{-- <x-input-label for="company" :value="__('Company')" />
            <x-text-input wire:model="company" id="company" name="company" type="text" class="mt-1 block w-full" required   autocomplete="company" />
            <x-input-error class="mt-2" :messages="$errors->get('company')" /> --}}

             <x-inputs.text-dropdown
                name="company"
                label="Company"
                :value="$company"
                placeholder="Search or select company..."
                :options="$companies"    
                wire:model.live="company"       
            />

            <x-input-error class="mt-2" :messages="$errors->get('company')" />

        </div>

        <div>
            <x-input-label for="address" :value="__('Address')" />
            <x-text-input wire:model="address" id="address" name="address" type="text" class="mt-1 block w-full" required   autocomplete="address" />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>



        <div>
            <x-input-label for="phone_number" :value="__('Phone number')" />
            <x-text-input wire:model="phone_number" id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" required   autocomplete="phone_number" />
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>


        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>


        


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
