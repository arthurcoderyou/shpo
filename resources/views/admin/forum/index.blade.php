<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Forums') }}
        </h2>
    </x-slot>

    <livewire:admin.forum.forum-create />
    <livewire:admin.forum.forum-list />


    




</x-app-layout>
