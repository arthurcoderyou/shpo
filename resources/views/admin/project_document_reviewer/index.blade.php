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

 
    <livewire:admin.project-reviewer.project-reviewer-list :project_id="$project->id" :project_document_id="$project_document->id" />
    
    {{-- <livewire:admin.project-document-reviewer.project-document-reviewer-list :project_document_id="$project_document->id" /> --}}


    @if($project_document->status !== "draft")
        <livewire:admin.review.review-list :id="$project->id" :project_document_id="$project_document->id" /> 

  
        <div  id="discussion" class=" px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
            <!-- Section Title -->
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





</x-app-layout>