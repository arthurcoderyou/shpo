<x-app-layout>
    
    @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute();
    @endphp 

    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ],
        ['label' => 'Projects', 'url' => $url],
        ['label' => 'Create New Project', 'url' => '#'],
    ]" />




    


    <livewire:partials.projects.page-header-title-only page-title="Create New Project" />
 
    
    <livewire:admin.project.project-create /> 



</x-app-layout>
