<x-app-layout>
    @php 
        // $url = App\Helpers\ProjectHelper::returnHomeProjectRoute();
    @endphp 

    @php
        $items = [
            ['label' => 'Home', 'url' => route('dashboard'), ], 
            ['label' => 'Re-Review Request', 'url' => "#"], 


        ];
  
        // if($project){

            
        // }else{
        //     $items[] = ['label' => 'Project Documents', 'url' => route('project.project_documents')];
        // }


        $items[] = ['label' => 'Review', 'url' => '#'];
    @endphp

    <x-breadcrumb :items="$items" />
  

    <livewire:admin.re-review.re-review-list />
     
 
 
</x-app-layout>