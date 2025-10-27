<x-app-layout>
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Roles', 'url' => route('role.index')], 
        ['label' => 'Update Permissions to Role '.$role->name, 'url' => '#'],
    ]" />

    <livewire:admin.role.add-permissions :role_id="$role->id" />



</x-app-layout>
