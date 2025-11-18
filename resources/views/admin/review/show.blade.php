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
        $items[] = ['label' => 'Review Workflow', 'url' => '#' ];
         

    @endphp

    <x-breadcrumb :items="$items" />

    

    {{-- @if(Auth::user()->hasRole('Admin') || Auth::user()->can('project reviewer edit') || Auth::user()->hasRole('DSI God Admin'))
    <livewire:admin.project-reviewer.project-reviewer-create :id="$project->id" />

    @endif --}}
 
    <!-- Header Card -->
    <livewire:partials.projects.page-header :project="$project" :project_document="$project_document"/>




    <x-ui.accordion.accordion open="0" class="max-w-full mx-auto p-4 sm:px-6 lg:px-8  mx-auto   gap-x-2 ">

        <x-ui.accordion.item 
            id="1" 
            title="Project"
        >
            <livewire:admin.project.project-show :id="$project->id" /> 
        </x-ui.accordion.item>

        <x-ui.accordion.item 
            id="2" 
            title="Document"
        >
            <livewire:admin.project-document.project-document-show :project_id="$project->id" :project_document_id="$project_document->id" /> 
        </x-ui.accordion.item>
 
    </x-accordion>
 
    <livewire:admin.project-document-reviewer.review-flow :project_document_id="$project_document->id" />



</x-app-layout>