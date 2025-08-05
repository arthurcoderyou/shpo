<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight"> 
            Create new Project Document  <!-- This is not visible -->
        </h2>
    </x-slot> 

    <livewire:admin.project-document.project-document-create :id="$project->id"  /> 


    @if($project->status !== "draft")
        <div  id="discussion" class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
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


    
    

    <div id="project_logs" class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8 mx-auto  "> 
        <h2 class="text-2xl text-center font-semibold text-gray-800">Project Logs</h2>
        <p class="text-truncate text-center text-gray-500">
            Project: <span class="font-bold text-black">{{ $project->name }}</span>
        </p> 
        <livewire:admin.project.project-logs :project_id="$project->id"   />
    </div>

</x-app-layout>
