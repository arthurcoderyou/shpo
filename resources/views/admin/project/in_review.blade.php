<x-app-layout>
    @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute();
        
    @endphp 

    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ],
        ['label' => 'Projects', 'url' => $url],
        ['label' => 'Edit Project', 'url' => '#'],
    ]" />

    {{-- <livewire:admin.project.project-review-list />  --}}
    <livewire:admin.project.project-list status="in_review_projects" :myProjects="false" /> 


</x-app-layout>
