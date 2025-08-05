<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Edit Role') }}
        </h2>
    </x-slot>


    <livewire:admin.user.user-edit :id="$user->id" />



</x-app-layout>
