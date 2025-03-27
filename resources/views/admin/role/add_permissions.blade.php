<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Roles') }}
        </h2>
    </x-slot>

    <livewire:admin.role.add-permissions :role_id="$role->id" />



</x-app-layout>
