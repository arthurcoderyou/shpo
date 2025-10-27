<x-app-layout> 
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Document Types', 'url' => '#'],
    ]" />


    @if(Auth::user()->can('system access global admin') || Auth::user()->can('document type create') )
        <livewire:admin.document-type.document-type-create />
    @endif


    <livewire:admin.document-type.document-type-list />
    


 



</x-app-layout>
