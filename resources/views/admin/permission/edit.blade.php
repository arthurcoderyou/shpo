<x-app-layout>
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Permissions', 'url' => route('permission.index')], 
        ['label' => 'Edit '.$permission->name, 'url' => '#'],
    ]" />

    <livewire:admin.permission.permission-edit :id="$permission->id" />



</x-app-layout>
