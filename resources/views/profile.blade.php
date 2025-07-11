<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">


            @if(Auth::user()->hasRole('DSI God Admin')   )
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <livewire:profile.test-component />
                    </div>
                </div>
            @endif

            @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->can('profile update information'))
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <livewire:profile.update-profile-information-form />
                    </div>
                </div>
            @endif

            @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->can('profile update password'))
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>
            @endif

            @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->can('profile delete account'))
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
            @endif 
        </div>
    </div>
</x-app-layout>
