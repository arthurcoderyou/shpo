<x-app-layout>
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Roles', 'url' => route('role.index')], 
        ['label' => 'Edit '.$role->name, 'url' => '#'],
    ]" />

    <livewire:admin.role.role-edit :id="$role->id" />



</x-app-layout>
