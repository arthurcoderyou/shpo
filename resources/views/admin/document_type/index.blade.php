<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Document Types ') }}
        </h2>
    </x-slot>


    @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('document type create') )
        <livewire:admin.document-type.document-type-create />
    @endif


    <livewire:admin.document-type.document-type-list />
    






</x-app-layout>
