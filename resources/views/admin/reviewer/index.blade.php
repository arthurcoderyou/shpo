<x-app-layout>
    {{-- Connected records  --}}
    
   <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Reviewers ', 'url' => '#'],
    ]" />




    <livewire:dashboard.project-requirements-panel />

    {{-- @if( Auth::user()->hasRole('system access global admin') || Auth::user()->hasPermissionTo('reviewer create') )
        <livewire:admin.reviewer.reviewer-create />
    @endif --}}


 

    @if( Auth::user()->can('system access global admin') || Auth::user()->can('reviewer list view') )
        <livewire:admin.reviewer.reviewer-list />
    @endif



</x-app-layout>
