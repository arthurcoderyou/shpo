 
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    
    {{-- <livewire:2-f-a.mark-as-trusted /> --}}
    <!-- Code for when the user has roles -->
        
    @if(Auth::user()->roles->isEmpty())

        <!-- User No Role Dashboard -->
            <livewire:dashboard.guest-dashboard />
        <!-- ./ User No Role Dashboard -->

    @else

        <livewire:dashboard.project-requirements-panel /> 


        <!-- Main Dashboard -->
            <livewire:dashboard.main-dashboard />

        <!-- ./ Main Dashboard -->
    @endif
     
    


    {{-- <livewire:dashboard.project-requirements-panel /> --}}




    {{-- <livewire:dashboard.guest-dashboard />  
    @if (Auth::user()->can('system access global admin') || Auth::user()->hasPermissionTo('dashboard view'))

        {{-- <livewire:dashboard.project-requirements-panel /> 

        @if (Auth::user()->can('system access global admin') || Auth::user()->hasPermissionTo('dashboard counters'))
            
            @if(Auth::user()->can('system access global admin') || Auth::user()->hasRole('Admin'))
                <!-- Code for when the user has the 'Admin' role -->
                <livewire:dashboard.admin-dashboard />
            @elseif(Auth::user()->hasRole('Reviewer'))
                <!-- Code for when the user has the 'Reviewer' role -->
                <livewire:dashboard.reviewer-dashboard />
            @elseif(Auth::user()->hasRole('User'))
                <!-- Code for when the user has the 'User' role -->
                <livewire:dashboard.user-dashboard />
            @endif
        @endif
        


        @if (Auth::user()->can('system access global admin') || Auth::user()->hasPermissionTo('dashboard notifications'))
            <livewire:notification.notification-list />
        @endif

    @endif
    --}}

    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
  
</x-app-layout>
