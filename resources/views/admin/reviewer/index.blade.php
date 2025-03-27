<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reviewers') }}
        </h2>
    </x-slot>
    @if( Auth::user()->hasRole('DSI God Admin') || Auth::user()->hasPermissionTo('reviewer create') )
        <livewire:admin.reviewer.reviewer-create />
    @endif
    @if( Auth::user()->hasRole('DSI God Admin') || Auth::user()->hasPermissionTo('reviewer list view') )
        <livewire:admin.reviewer.reviewer-list />
    @endif



</x-app-layout>
