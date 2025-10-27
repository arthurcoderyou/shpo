<x-app-layout>
    @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute();
    @endphp 

    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ],
        ['label' => 'Projects', 'url' => $url],
        ['label' => 'Edit Project', 'url' => '#'],
    ]" />

    {{-- <livewire:admin.project.project-pending-update-list />  --}}
    <livewire:admin.project.project-list status="pending_update_projects" :myProjects="false" /> 


</x-app-layout>
