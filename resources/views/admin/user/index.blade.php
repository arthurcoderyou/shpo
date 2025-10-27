<x-app-layout>
     <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Users', 'url' => '#'], 
    ]" />

    <livewire:admin.user.user-list />



</x-app-layout>
