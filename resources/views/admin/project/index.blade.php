<x-app-layout>
    

    @php
        $page_title = App\Helpers\ProjectHelper::updateTitleAndSub($route);
    @endphp

    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => $page_title, 'url' => '#'],
    ]" />

    <livewire:admin.project.project-list :route="$route" :myProjects="true"  /> 



</x-app-layout>
