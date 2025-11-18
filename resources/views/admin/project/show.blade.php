<x-app-layout>
   @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute($project);
    @endphp 

    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ],
        ['label' => 'Projects', 'url' => $url],
        ['label' => $project->name, 'url' => '#'],
    ]" />
    

    <!-- Header Card -->
    <livewire:partials.projects.page-header :project="$project" />

  
    <livewire:admin.project.project-show :id="$project->id" /> 



    {{-- @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->hasRole('Reviewer') )
        <livewire:admin.review.review-create :id="$project->id" /> 
    @endif
     --}}


    <!-- only show project review list and dicussions when the project is not draft -->
    @if($project->status !== "draft")

        <livewire:admin.review.review-list :id="$project->id"  /> 
         
 
            
        <div  id="discussion" class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
            
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
