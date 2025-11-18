<x-app-layout>
     
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

        // $items[] = ['label' => 'Project Documents', 'url' => '#'];

        $items[] = ['label' => 'Edit Project', 'url' => '#'];

    @endphp

    <x-breadcrumb :items="$items" />
    

 
    <!-- Header Card -->
    <livewire:partials.projects.page-header :project="$project" />

    <livewire:admin.project.project-edit :id="$project->id" /> 


     <!-- only show project review list and dicussions when the project is not draft -->
    @if($project->status !== "draft") 


        <livewire:admin.review.review-list :id="$project->id" /> 

        <div  id="discussion" class=" px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
            <!-- Section Title -->
            <div class="  border-b pb-4">
                <h2 class="text-2xl text-center font-semibold text-gray-800">Project Discussions</h2>
                <p class="text-truncate text-center">{{ $project->name }}</p>
                @if($project->canPostInDiscussion())
                    <livewire:admin.project-discussion.project-discussion-create :project="$project" />
                @endif
            </div>
        </div>

        <livewire:admin.project-discussion.project-discussion-list :project="$project" />

    @endif

    <div id="project_logs" class="  py-6     mx-auto  "> 
        <h2 class="text-2xl text-center font-semibold text-gray-800">Project Logs</h2>
        <p class="text-truncate text-center">{{ $project->name }}</p>
        <livewire:admin.project.project-logs :project_id="$project->id" />
    </div>

</x-app-layout>
