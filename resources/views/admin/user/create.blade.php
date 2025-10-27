<x-app-layout>
     <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Users', 'url' => route('user.index')], 
        ['label' => 'Create New User', 'url' => '#'],
    ]" />


    <livewire:admin.user.user-create />



</x-app-layout>
