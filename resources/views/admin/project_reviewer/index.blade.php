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


    {{-- @if(Auth::user()->hasRole('Admin') || Auth::user()->can('project reviewer edit') || Auth::user()->hasRole('DSI God Admin'))
    <livewire:admin.project-reviewer.project-reviewer-create :id="$project->id" />

    @endif --}}

    


    
    <livewire:admin.project-reviewer.project-reviewer-list :id="$project->id" />



</x-app-layout>