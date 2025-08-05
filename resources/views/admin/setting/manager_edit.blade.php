<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl  leading-tight">
            {{ __('Settings Manager Update ') }}
        </h2>
    </x-slot>
    
    
    <!-- setting manager -->
    <livewire:setting.setting-manager-edit :id="$setting->id" />


</x-app-layout>
                                                                                                                                                                                                                    