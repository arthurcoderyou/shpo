<x-app-layout>

    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Update Project Time Settings', 'url' => '#'], 
    ]" />

    
    @if(Auth::user()->can('system access admin') || Auth::user()->can('system access global admin') ||  Auth::user()->can('timer list view'))
        <livewire:admin.project-timer.project-timer-edit  />

    @endif 



</x-app-layout>