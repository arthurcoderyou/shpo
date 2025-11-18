<x-app-layout>
    @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute();
    @endphp 

    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Projects', 'url' => $url],
        ['label' => $project->name, 'url' => route('project.show',['project' => $project->id])],
        // ['label' => 'Project Documents', 'url' => route('project-document.index')],
        ['label' => 'Project Documents', 'url' => route('project.project_documents',['project' => $project->id])],
        ['label' => $project_document->document_type->name, 'url' => route('project.project-document.show', ['project' => $project->id, 'project_document' => $project_document->id]) ],
        ['label' => 'Edit Project Document', 'url' => '#'],
    ]" />


    <!-- Header Card -->
    <livewire:partials.projects.page-header :project="$project" :project_document="$project_document" />
 
    <livewire:dashboard.project-requirements-panel />


    <livewire:admin.project-document.project-document-edit :project_id="$project->id" :project_document_id="$project_document->id"  /> 


    @if($project->status !== "draft")
        <div  id="discussion" class=" px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
            {{-- Section Title --}}
            <div class="  border-b pb-4">
                <h2 class="text-2xl text-center font-semibold text-gray-800">Project Discussions</h2>
                <p class="text-truncate text-center text-gray-500">
                    Project: <span class="font-bold text-black">{{ $project->name }}</span>
                </p> 
                @if($project->canPostInDiscussion())
                    <livewire:admin.project-discussion.project-discussion-create :project="$project"   />
                @endif
            </div>
        </div>


        <livewire:admin.project-discussion.project-discussion-list :project="$project" />

    @endif


    
    

    <div id="project_logs" class="  py-6     mx-auto  "> 
        <h2 class="text-2xl text-center font-semibold text-gray-800">Project Logs</h2>
        <p class="text-truncate text-center text-gray-500">
            Project: <span class="font-bold text-black">{{ $project->name }}</span>
        </p> 
        <livewire:admin.project.project-logs :project_id="$project->id"   />
    </div>

</x-app-layout>
