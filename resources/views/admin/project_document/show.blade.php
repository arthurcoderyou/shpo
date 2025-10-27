<x-app-layout>

     

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


        // $items[] = ['label' => $project_document->document_type->name, 'url' => '#'];
    @endphp

    <x-breadcrumb :items="$items" />




    <!-- Header Card -->
    <livewire:partials.projects.page-header :project="$project" :project_document="$project_document" />

    <livewire:dashboard.project-requirements-panel />

    <livewire:admin.project-document.project-document-show :project_id="$project->id" :project_document_id="$project_document->id" /> 


    @if($project_document->status !== "draft")
        <livewire:admin.review.review-list :id="$project->id" :project_document_id="$project_document->id"  /> 

        <div  id="discussion" class=" px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
            {{-- Section Title --}}
            <div class="  border-b pb-4">
                <h2 class="text-2xl text-center font-semibold text-gray-800">Project Discussions</h2>
                <p class="text-truncate text-center text-gray-500">
                    Project: <span class="font-bold text-black">{{ $project->name }}</span>
                </p>
                <p class="text-truncate text-center text-gray-500">
                    Document: <span class="font-bold text-black">{{ $project_document->document_type->name }}</span> 
                </p>
                
                @if($project->canPostInDiscussion())
                    <livewire:admin.project-discussion.project-discussion-create :project="$project" :project_document="$project_document" />
                @endif
            </div>
        </div>


        <livewire:admin.project-discussion.project-discussion-list :project="$project" :project_document="$project_document" />

    @endif


    
    

    <div id="project_logs" class=" px-4 py-6 sm:px-6 lg:px-8 mx-auto  "> 
        <h2 class="text-2xl text-center font-semibold text-gray-800">Project Logs</h2>
        <p class="text-truncate text-center text-gray-500">
            Project: <span class="font-bold text-black">{{ $project->name }}</span>
        </p>
        <p class="text-truncate text-center text-gray-500">
            Document: <span class="font-bold text-black">{{ $project_document->document_type->name }}</span> 
        </p>
        <livewire:admin.project.project-logs :project_id="$project->id" :project_document_id="$project_document->id" />
    </div>

</x-app-layout>
