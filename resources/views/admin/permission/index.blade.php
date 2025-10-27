<x-app-layout>
     
     <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Permissions', 'url' => '#'], 
    ]" />

    <livewire:admin.permission.permission-list />



</x-app-layout>
