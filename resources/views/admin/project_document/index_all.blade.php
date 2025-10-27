<x-app-layout>
    {{--  --}}
    
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ],
        ['label' => 'Projects Documents', 'url' => '#'], 
    ]" />

    <livewire:admin.project-document.project-document-list />

</x-app-layout>
