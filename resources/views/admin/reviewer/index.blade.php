<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reviewers') }}
        </h2>
    </x-slot>


    <livewire:dashboard.project-requirements-panel />

    {{-- @if( Auth::user()->hasRole('system access global admin') || Auth::user()->hasPermissionTo('reviewer create') )
        <livewire:admin.reviewer.reviewer-create />
    @endif --}}


 

    @if( Auth::user()->can('system access global admin') || Auth::user()->can('reviewer list view') )
        <livewire:admin.reviewer.reviewer-list />
    @endif



</x-app-layout>
