<x-app-layout>
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Settings Keys', 'url' => '#'], 
    ]" />
    
    
    <!-- setting manager -->
    <livewire:setting.setting-manager-edit :id="$setting->id" />


</x-app-layout>
                                                                                                                                                                                                                    