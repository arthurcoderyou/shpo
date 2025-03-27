<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Document Types ') }}
        </h2>
    </x-slot>

    <livewire:admin.document-type.document-type-edit :id="$document_type->id" />
    <livewire:admin.document-type.document-type-list />
    
 

</x-app-layout>
