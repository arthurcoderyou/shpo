<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Projects') }}
        </h2>
    </x-slot>
    <livewire:admin.project.project-edit :id="$project->id" /> 


     <!-- only show project review list and dicussions when the project is not draft -->
    @if($project->status !== "draft") 
        <div  id="discussion" class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
            <!-- Section Title -->
            <div class="  border-b pb-4">
                <h2 class="text-2xl text-center font-semibold text-gray-800">Project Discussions</h2>
                <p class="text-truncate text-center">{{ $project->name }}</p>
                @if($project->canPostInDiscussion())
                    <livewire:admin.project-discussion.project-discussion-create :project="$project" />
                @endif
            </div>
        </div>

    @endif

    <div id="project_logs" class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8 mx-auto  "> 
        <h2 class="text-2xl text-center font-semibold text-gray-800">Project Logs</h2>
        <p class="text-truncate text-center">{{ $project->name }}</p>
        <livewire:admin.project.project-logs :project_id="$project->id" />
    </div>

</x-app-layout>
