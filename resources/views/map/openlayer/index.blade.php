<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Open Layer Map') }}
        </h2>
    </x-slot>

    <livewire:map.openlayer.map-list />



</x-app-layout>
