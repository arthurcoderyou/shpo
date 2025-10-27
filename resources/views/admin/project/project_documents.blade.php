<x-app-layout>
    
    
    @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute();
    @endphp 

    @php
        $items = [
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Projects', 'url' => $url],
        ];

        if (!empty($project)) {
            $items[] = ['label' => $project->name, 'url' => route('project.show', $project->id)];
        }

        $items[] = ['label' => 'Project Documents', 'url' => '#'];
    @endphp

    <x-breadcrumb :items="$items" />

    <livewire:admin.project-document.project-document-list  :route="$route"  :project_id="$project ? $project->id : null" /> 

    

</x-app-layout>
