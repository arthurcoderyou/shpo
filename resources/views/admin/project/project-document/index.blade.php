<x-app-layout>

    @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute();
    @endphp 

    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Projects', 'url' => $url],
        ['label' => $project->name, 'url' => route('project.show',['project' => $project->id])],
        ['label' => 'Project Documents', 'url' => '#'],
        // ['label' => 'Project Documents', 'url' => route('project.project_documents')],
        //  ['label' => $project_document->document_type->name, 'url' => '#'],
    ]" />

    <!-- Header Card -->
    <livewire:partials.projects.page-header :project="$project" />
    
    <livewire:dashboard.project-requirements-panel />

    {{-- <livewire:admin.project.project-document.project-document-list :project_id="$project->id"   />  --}}
 
    <livewire:admin.project-document.project-document-list :project_id="$project->id"  route="project.project-document.index"  /> 

</x-app-layout>
