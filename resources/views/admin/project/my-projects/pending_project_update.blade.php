<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pending Project Update') }}
        </h2>
    </x-slot> 

    {{-- <livewire:admin.project.project-pending-update-list />  --}}
    <livewire:admin.project.project-list status="pending_update_projects" :myProjects="true" /> 


</x-app-layout>
