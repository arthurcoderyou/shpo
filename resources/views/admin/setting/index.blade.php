<x-app-layout>
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Setting Manager', 'url' => '#'], 
    ]" />

    <livewire:setting.setting-create />
    <livewire:setting.setting-list />

    

</x-app-layout>
                                                                                                                                                                                                                    