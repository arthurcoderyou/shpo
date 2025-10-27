<x-app-layout>
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Roles', 'url' => '#'], 
    ]" />


    <livewire:admin.role.role-list />



</x-app-layout>
