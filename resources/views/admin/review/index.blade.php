<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reviews') }}
        </h2>
    </x-slot>

    
    @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('review list view'))
        <livewire:admin.review.review-list  />

    @endif 



</x-app-layout>