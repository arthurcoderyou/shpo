<x-app-layout>
     <!-- #endregion -->

    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Permissions', 'url' => route('permission.index')], 
        ['label' => 'Create New Permission', 'url' => '#'],
    ]" />

    <livewire:admin.permission.permission-create />
 
</x-app-layout>
