<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Project Timers') }}
        </h2>
    </x-slot>
    {{-- @if(Auth::user()->hasRole('Admin'))
    <livewire:admin.project-reviewer.project-reviewer-create :id="$project->id" />

    @endif
    <livewire:admin.project-reviewer.project-reviewer-list :id="$project->id" /> --}}



</x-app-layout>