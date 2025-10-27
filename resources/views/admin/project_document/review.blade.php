<x-app-layout>
    {{--  --}}
    
    {{-- <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ],
        ['label' => 'Projects Document Review', 'url' => '#'], 
    ]" /> --}}
    @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute();
    @endphp 

    @php
        $items = [
            ['label' => 'Home', 'url' => route('dashboard'), ], 
            ['label' => 'Projects', 'url' => $url],
            ['label' => $project->name, 'url' => route('project.show',['project' => $project->id])],
            // ['label' => 'Project Documents', 'url' => route('project-document.index')],
            ['label' => 'Project Documents', 'url' => route('project.project_documents',['project' => $project->id])],
            ['label' => $project_document->document_type->name, 'url' => '#'],
        ];
  
        // if($project){

            
        // }else{
        //     $items[] = ['label' => 'Project Documents', 'url' => route('project.project_documents')];
        // }


        $items[] = ['label' => 'Review', 'url' => '#'];
    @endphp

    <x-breadcrumb :items="$items" />

    <!-- Header Card -->
    <livewire:partials.projects.page-header :project="$project" :project_document="$project_document" />



    <livewire:admin.project-document.project-document-review :project_id="$project->id" :project_document_id="$project_document->id" />
     

    {{-- @if($project->getCurrentReviewer()->user_id == auth()->user()->id) --}}
        <livewire:admin.review.review-create :id="$project->id" :project_document_id="$project_document->id" /> 
    {{-- @endif --}}

    
    <livewire:admin.review.review-list :id="$project->id" :project_document_id="$project_document->id"  /> 



    {{-- <livewire:admin.review.review-list :id="$project->id" />  --}}
        
    <div  id="discussion" class="  px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
        {{-- Section Title --}}
        <div class="  border-b pb-4">
            <h2 class="text-2xl text-center font-semibold text-gray-800">Project Discussions</h2>
            <p class="text-truncate text-center">{{ $project->name }}</p>
            @if($project->canPostInDiscussion())
                <livewire:admin.project-discussion.project-discussion-create :project="$project" />
            @endif
        </div>
    </div>

    <livewire:admin.project-discussion.project-discussion-list :project="$project" />


    <div id="project_logs" class="  px-4 py-6 sm:px-6 lg:px-8 mx-auto  "> 
        <h2 class="text-2xl text-center font-semibold text-gray-800">Project Logs</h2>
        <p class="text-truncate text-center">{{ $project->name }}</p>
        <livewire:admin.project.project-logs :project_id="$project->id" />
    </div>


</x-app-layout>
