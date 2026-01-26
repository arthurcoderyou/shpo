<x-app-layout>


 
    {{-- Connected records  --}}
    @php 
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute($project);
    @endphp 

    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ],
        ['label' => 'Projects', 'url' => $url],
        ['label' => 'Reviewers for '.$project->name, 'url' => '#'],
    ]" />


    <!-- Header Card -->
    <livewire:partials.projects.page-header :project="$project" />





    {{-- @if(Auth::user()->hasRole('Admin') || Auth::user()->can('project reviewer edit') || Auth::user()->hasRole('DSI God Admin'))
    <livewire:admin.project-reviewer.project-reviewer-create :id="$project->id" />

    @endif --}}

    


    
    <livewire:admin.project-reviewer.project-reviewer-list :project_id="$project->id" />

     @if($project->status !== "draft")
        <livewire:admin.review.review-list :id="$project->id"  /> 

  
        <div  id="discussion" class=" px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
            <!-- Section Title -->
            <div class="  border-b pb-4">
                <h2 class="text-2xl text-center font-semibold text-gray-800">Project Discussions</h2>
                <p class="text-truncate text-center text-gray-500">
                    Project: <span class="font-bold text-black">{{ $project->name }}</span>
                </p> 
                
                @if($project->canPostInDiscussion())
                    <livewire:admin.project-discussion.project-discussion-create :project="$project"  />
                @endif
            </div>
        </div>
          

        <livewire:admin.project-discussion.project-discussion-list :project="$project"  />
        

    @endif



</x-app-layout>