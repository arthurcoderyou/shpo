<x-app-layout>

    @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute();
    @endphp 

    @php
        // Read from route segments, fall back to query string (?project=...&project_document=...)
        $routeProjectId        = request()->route('project') ?? request('project');
        $routeProjectDocumentId= request()->route('project_document') ?? request('project_document');


        $items = [
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Projects', 'url' => $url],
        ];
  

        $label = 'Project Documents';
        if($route == "project-document.index.open-review"){
            $label = "Open Review Project Documents";
        }

        $items[] = ['label' => $label, 'url' => '#'];
    @endphp

    <x-breadcrumb :items="$items" />

    <livewire:admin.project-document.project-document-list  
        :route="$route ?? 'project-document.index'" 
        :project_id="$routeProjectId"
        :project_document_id="$routeProjectDocumentId"
        
        /> 
 
    

</x-app-layout>
