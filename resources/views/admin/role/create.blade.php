<x-app-layout>
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Roles', 'url' => route('role.index')], 
        ['label' => 'Create New Role ', 'url' => '#'],
    ]" />

    <livewire:admin.role.role-create />



</x-app-layout>
