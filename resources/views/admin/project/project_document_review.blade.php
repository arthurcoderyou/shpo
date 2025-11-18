<x-app-layout>
    {{-- <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Projects') }}
        </h2>
    </x-slot>
    <livewire:admin.project.project-review :id="$project->id" /> 

    
    <livewire:admin.review.review-create :id="$project->id" /> 


    
    <livewire:admin.review.review-list :id="$project->id" /> 
        

    <div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
        <!-- Section Title -->
        <div class="  border-b pb-4">
            <h2 class="text-2xl text-center font-semibold text-gray-800">Project Discussions</h2>
            <p class="text-truncate text-center">{{ $project->name }}</p>
            @if($project->canPostInDiscussion())
                <livewire:admin.project-discussion.project-discussion-create :project="$project" />
            @endif
        </div>
    </div>

    <livewire:admin.project-discussion.project-discussion-list :project="$project" /> --}}



    @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute($project);
    @endphp 

   
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Projects', 'url' => $url],
        ['label' => $project->name, 'url' => route('project.show',['project' => $project->id])],
        ['label' => 'Project Documents', 'url' => route('project.project_documents')],
        ['label' => $project_document->document_type->name, 'url' => route('project.project_document', ['project' => $project_document->project->id, 'project_document' => $project_document->id]) ],
        ['label' => 'Review', 'url' => '#'],
    ]" />
 

    {{-- <livewire:admin.project.project-show :id="$project->id" />  --}}



    <!-- Check if the current auth user is the active reviewer-->

    {{-- @if($project->getCurrentReviewer()->user_id == auth()->user()->id) --}}
        <livewire:admin.review.review-create :id="$project->id" :project_document_id="$project_document->id" /> 
    {{-- @endif --}}

    
    <livewire:admin.review.review-list :id="$project->id" /> 



    {{-- <livewire:admin.review.review-list :id="$project->id" />  --}}
        
    <div  id="discussion" class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
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


    <div id="project_logs" class="  py-6     mx-auto  ">  
        <h2 class="text-2xl text-center font-semibold text-gray-800">Project Logs</h2>
        <p class="text-truncate text-center">{{ $project->name }}</p>
        <livewire:admin.project.project-logs :project_id="$project->id" />
    </div>


</x-app-layout>
