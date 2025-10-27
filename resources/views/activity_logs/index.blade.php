<x-app-layout>
     
     <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Activity Logs', 'url' => '#'],
    ]" />

    <livewire:activity-logs.activity-logs-list />



</x-app-layout>
