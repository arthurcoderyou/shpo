<x-app-layout>
     <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Users', 'url' => route('user.index')], 
        ['label' => 'Edit '.$user->name, 'url' => '#'],
    ]" />


    <livewire:admin.user.user-edit :id="$user->id" />



</x-app-layout>
