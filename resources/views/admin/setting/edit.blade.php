<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl  leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <livewire:setting.setting-edit :id="$setting->id" />
    <livewire:setting.setting-list />



</x-app-layout>
