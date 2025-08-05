<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Project Timers') }}
        </h2>
    </x-slot>

    
    @if(Auth::user()->can('system access admin') || Auth::user()->can('system access global admin') ||  Auth::user()->can('timer list view'))
        <livewire:admin.project-timer.project-timer-edit  />

    @endif 



</x-app-layout>