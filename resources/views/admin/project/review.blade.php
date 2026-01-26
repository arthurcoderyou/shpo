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
        $url = App\Helpers\ProjectHelper::returnHomeProjectRoute();
    @endphp 

     <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ],
        ['label' => 'Projects', 'url' => $url],
        ['label' => 'Review Project', 'url' => '#'],
    ]" />

 
    <livewire:partials.projects.page-header :project="$project"  /> 



    <!-- Check if the current auth user is the active reviewer-->

    {{-- @if($project->getCurrentReviewer()->user_id == auth()->user()->id) --}}
        <livewire:admin.review.project.review-create :id="$project->id" /> 
    {{-- @endif --}}

    
    <livewire:admin.review.review-list :id="$project->id" /> 



    {{-- <livewire:admin.review.review-list :id="$project->id" />  --}}
        
    <div  id="discussion" class="max-w-full px-4 py-6 sm:px-6 lg:px-8 mx-auto space-y-6">
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
