<x-app-layout>
     

    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Profile', 'url' => '#'],
    ]" />

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">


            @if(Auth::user()->can('system access global admin')   )
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <livewire:profile.test-component />
                    </div>
                </div>
            @endif

            @if(Auth::user()->can('system access global admin')  || Auth::user()->can('profile update information'))
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <livewire:profile.update-profile-information-form />
                    </div>
                </div>
            @endif

            @if(Auth::user()->can('system access global admin')  || Auth::user()->can('profile update password'))
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>
            @endif

            @if(Auth::user()->can('system access global admin')  || Auth::user()->can('profile delete account'))
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
            @endif 
        </div>
    </div>
</x-app-layout>
