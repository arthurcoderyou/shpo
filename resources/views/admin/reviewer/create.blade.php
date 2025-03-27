<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reviewers') }}
        </h2>
    </x-slot>

    <livewire:admin.permission.permission-list />



</x-app-layout>
