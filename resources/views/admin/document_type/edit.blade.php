<x-app-layout>
     <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Document Types', 'url' => route('document_type.index')],
        ['label' => 'Edit '.$document_type->name, 'url' => '#'],
    ]" />

    <livewire:admin.document-type.document-type-edit :id="$document_type->id" />
    <livewire:admin.document-type.document-type-list />
    
 

</x-app-layout>
