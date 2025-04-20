 
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    
    {{-- <livewire:2-f-a.mark-as-trusted /> --}}


    @if(Auth::user()->roles->isEmpty())
        {{-- Code for when the user has roles --}}
        <livewire:dashboard.guest-dashboard />
    @endif


    {{-- <livewire:dashboard.guest-dashboard /> --}}
    @if (Auth::user()->hasRole('DSI God Admin') || Auth::user()->hasPermissionTo('dashboard view'))

        <livewire:dashboard.project-requirements-panel />

        @if (Auth::user()->hasRole('DSI God Admin') || Auth::user()->hasPermissionTo('dashboard counters'))
            
            @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->hasRole('Admin'))
                {{-- Code for when the user has the 'Admin' role --}}
                <livewire:dashboard.admin-dashboard />
            @elseif(Auth::user()->hasRole('Reviewer'))
                {{-- Code for when the user has the 'Reviewer' role --}}
                <livewire:dashboard.reviewer-dashboard />
            @elseif(Auth::user()->hasRole('User'))
                {{-- Code for when the user has the 'User' role --}}
                <livewire:dashboard.user-dashboard />
            @endif
        @endif
        


        @if (Auth::user()->hasRole('DSI God Admin') || Auth::user()->hasPermissionTo('dashboard notifications'))
            <livewire:notification.notification-list />
        @endif

    @endif
</x-app-layout>
