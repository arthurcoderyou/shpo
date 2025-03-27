<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Projects') }}
        </h2>
    </x-slot>
    <livewire:admin.project.project-show :id="$project->id" /> 
    <livewire:admin.review.review-list :id="$project->id" /> 


</x-app-layout>
