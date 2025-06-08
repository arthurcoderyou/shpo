<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Project Reviewers') }}
        </h2>
    </x-slot>
    {{-- @if(Auth::user()->hasRole('Admin') || Auth::user()->can('project reviewer edit') || Auth::user()->hasRole('DSI God Admin'))
    <livewire:admin.project-reviewer.project-reviewer-create :id="$project->id" />

    @endif --}}


    
    <livewire:admin.project-reviewer.project-reviewer-list :id="$project->id" />



</x-app-layout>