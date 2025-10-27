<x-app-layout>


 
    {{-- Connected records  --}}
    @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute($project);
    @endphp 
 

    @php
        $items = [
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Projects', 'url' => $url],
        ];

        if (!empty($project)) {
            $items[] = ['label' => $project->name, 'url' => route('project.show', $project->id)];
        } 
        $items[] = ['label' => 'Project Documents', 'url' => route('project-document.index')];
        $items[] = ['label' => $project_document->document_type->name, 'url' => route('project.project-document.show', ['project' => $project->id, 'project_document' => $project_document->id])];
        $items[] = ['label' => 'Document Reviewers', 'url' => '#' ];
         

    @endphp

    <x-breadcrumb :items="$items" />

    

    {{-- @if(Auth::user()->hasRole('Admin') || Auth::user()->can('project reviewer edit') || Auth::user()->hasRole('DSI God Admin'))
    <livewire:admin.project-reviewer.project-reviewer-create :id="$project->id" />

    @endif --}}
 
    <!-- Header Card -->
    <livewire:partials.projects.page-header :project="$project" :project_document="$project_document"/>

    
    <livewire:admin.project-document-reviewer.project-document-reviewer-list :project_document_id="$project_document->id" />



</x-app-layout>