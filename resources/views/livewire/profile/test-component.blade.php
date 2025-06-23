<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendEmailNotification;

new class extends Component
{
    public string $email = '';
    public string $message = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    { 
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    { 

        $this->validate([
            
            'email' => ['required', 'string', 'lowercase', 'email',  ],
            'message' => ['required', 'string', 'max:255'],
        ]);

        
 

        // $this->dispatch('email-sent', email: $this->email);
        session('success','Email sent successfully');
        // return redirect()->route('dashboard'); 
        $this->redirectIntended(default: route('profile', absolute: false));

       


    }


    public function send()
    {

        $this->validate([
            
            'email' => ['required', 'string', 'lowercase', 'email',  ],
            'message' => ['required', 'string', 'max:255'],
        ]);

        Notification::route('mail', $this->email)
            ->notify(new SendEmailNotification($this->message));


        // $this->dispatch('email-sent', email: $this->email);
        session('success','Email sent successfully');
        // return redirect()->route('dashboard'); 
        $this->redirectIntended(default: route('profile', absolute: false));
    }

 
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Test Component ') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Testing Grounds") }}
        </p>
    </header>

    <form wire:submit="send" class="mt-6 space-y-6">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" required autofocus autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="message" :value="__('Message')" />
            <x-text-input wire:model="message" id="message" name="message" type="text" class="mt-1 block w-full" required autocomplete="message" />
            <x-input-error class="mt-2" :messages="$errors->get('message')" />

             
                <div>
                    

                    @if (session('success') )
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('Email had been sent') }}
                        </p>
                    @endif
                </div> 
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="email-sent">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
