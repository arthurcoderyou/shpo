<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Discussions') }}
        </h2>
    </x-slot>  
    <livewire:admin.discussion.discussion-create /> 
</x-app-layout> 