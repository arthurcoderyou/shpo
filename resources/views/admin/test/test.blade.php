<x-app-layout>
    {{--  --}}
    
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ],
        ['label' => 'Testing Page: '.$page_label, 'url' => '#'], 
    ]" />

    @if(!empty($test_route))
        @switch($test_route)
            @case('test/project')
                    <livewire:admin.test.test-project-list />
                @break
            @case('test/project_document')
                    <livewire:admin.test.test-project-document-list />
                @break
            @case('test/project_document_review')
                    <livewire:admin.test.test-project-document-review />
                @break
            @case('test/project/table')
                    <livewire:admin.test.test-project-list-table />
                @break
            @case('test/project/show')
                    <livewire:admin.test.test-project-show />
                @break
            @case('test/project/show_2')
                    <livewire:admin.test.test-project-show2 />
                @break

            @case('test/review/list')
                    <livewire:admin.test.test-review-list />
                @break


                
        
            @default
                
        @endswitch
    @endif
    

</x-app-layout>
