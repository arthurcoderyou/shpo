<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Map') }}
        </h2>
    </x-slot>

    {{-- <livewire:admin.permission.permission-list /> --}}
    <livewire:admin.map.arcgis.map />


</x-app-layout>
