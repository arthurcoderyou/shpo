<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;
use App\Models\UserDeviceLog;
use App\Models\ActivityLog;
use RealRashid\SweetAlert\Facades\Alert;


new class extends Component
{

   
    public $show_device_trust_section = false;

    public function mount()
    {
        $user_device_log = UserDeviceLog::getUserDeviceLog();

        // Only show the section if the device is NOT trusted
        $this->show_device_trust_section = !$user_device_log->trusted;
    }

    public function markDeviceAsTrusted($answer)
    {
        $user_device_log = UserDeviceLog::getUserDeviceLog();
        $user_device_log->trusted = $answer === 'yes';
        $user_device_log->save();

        // Hide the section after marking the device
        $this->show_device_trust_section = false;


        if($answer === 'yes'){
            ActivityLog::create([
                'log_action' => "Deviee trusted by \"".Auth::user()->name."\" ",
                'log_username' => Auth::user()->name,
                'created_by' => Auth::user()->id,
            ]);

            Alert::success('Success','Device trusted successfully');
            return redirect()->route('dashboard');
        }
        




    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {

        // Destroy session
        Session::flush();
        
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    {{-- <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block w-52 fill-current text-gray-500" />
                    </a>
                </div>

                <!-- <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-2 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-2 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Sites') }}
                    </x-nav-link>
                </div> -->


            </div>


            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </x-nav-link>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class=" inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">

                            <div class="border-b-2 border-indigo-500" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div> --}}

    <!-- ========== HEADER ========== -->
    <header class="flex flex-wrap  md:justify-start md:flex-nowrap z-50 w-full bg-white border-b border-gray-200 ">
        <nav class="relative max-w-[85rem] w-full mx-auto md:flex md:items-center md:justify-between md:gap-3 py-2 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center gap-x-1">
            <a class="flex-none font-semibold text-xl text-black focus:outline-none focus:opacity-80 " href="#" aria-label="Brand">
                <input type="image" src="{{ asset('images/logo-ghrd.png') }}" class="max-w-52 sm:max-w-72" alt="">
            </a>

            <!-- Collapse Button -->
            <button type="button" class="hs-collapse-toggle md:hidden relative size-9 flex justify-center items-center font-medium text-[12px] rounded-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none  " id="hs-header-base-collapse"  aria-expanded="false" aria-controls="hs-header-base" aria-label="Toggle navigation"  data-hs-collapse="#hs-header-base" >
            <svg class="hs-collapse-open:hidden size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>
            <svg class="hs-collapse-open:block shrink-0 hidden size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            <span class="sr-only">Toggle navigation</span>
            </button>
            <!-- End Collapse Button -->
        </div>

        <!-- Collapse -->
        <div id="hs-header-base" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow md:block "  aria-labelledby="hs-header-base-collapse" >
            <div class="overflow-hidden overflow-y-auto max-h-[75vh] [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 ">
            <div class="py-2 md:py-0  flex flex-col md:flex-row md:items-center gap-0.5 md:gap-1">
                <div class="grow">
                    <div class="flex flex-col md:flex-row md:justify-end md:items-center gap-0.5 md:gap-1">
                        @php
                            $active = "bg-gray-800 text-white hover:bg-white hover:text-gray-800 focus:outline-none focus:text-white focus:bg-gray-800";
                            $inactive = "bg-white text-gray-800 hover:text-gray-800 hover:bg-gray-100 focus:outline-none focus:text-gray-800 focus:bg-gray-100 "
                        @endphp

                        @if($show_device_trust_section == true)
                        <div class="bg-gray-50 border border-gray-200 text-sm text-gray-600 rounded-lg p-2 " role="alert" tabindex="-1" aria-labelledby="hs-link-on-right-label">
                            <div class="flex">
                            <div class="shrink-0">
                                <svg class="shrink-0 size-4 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 16v-4"></path>
                                <path d="M12 8h.01"></path>
                                </svg>
                            </div>
                            <div class="flex-1 md:flex md:justify-between ms-2">
                                <p id="hs-link-on-right-label" class="text-sm">
                                    Trust this device?
                                </p>
                                <p class="text-sm  ms-6">
                                    <button wire:click="markDeviceAsTrusted('yes')" class="text-green-800 hover:text-gray-500 focus:outline-hidden focus:text-gray-500 font-medium whitespace-nowrap " href="#">
                                        Yes
                                    </button>
                                    <button wire:click="markDeviceAsTrusted('no')" class="text-red-800 hover:text-gray-500 focus:outline-hidden focus:text-gray-500 font-medium whitespace-nowrap " href="#">
                                        No
                                    </button>
                                </p>
                            </div>
                            </div>
                        </div>
                        @endif



                        <a class="p-2 flex items-center text-sm  rounded-lg  {{ request()->routeIs('dashboard') ? $active : $inactive }} "
                            href="{{ route('dashboard') }}"
                            aria-current="dashboard">
                            <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Dashboard
                        </a>

                        <!-- only show for users that has role -->
                        @if(Auth::user()->roles->isNotEmpty())
                            <!-- Projects -->
                            <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] [--is-collapse:true] md:[--is-collapse:false] ">
                                <button id="hs-header-base-dropdown" type="button" class="hs-dropdown-toggle w-full p-2 flex items-center text-sm  rounded-lg focus:outline-none
                                {{ request()->routeIs('project.index') || request()->routeIs('project.in_review') ||
                                    request()->routeIs('reviewer.index') || request()->routeIs('project.edit') || 
                                    request()->routeIs('project.create') || request()->routeIs('project.show') || 
                                    request()->routeIs('project.in_review') || request()->routeIs('project.review') || 
                                    request()->routeIs('project.reviewer.index') || 
                                    request()->routeIs('project_timer.index') || 
                                    request()->routeIs('document_type.index') || request()->routeIs('document_type.edit') || 
                                    request()->routeIs('project.pending_project_update') || 
                                    request()->routeIs('review.index') 

                                ? $active : $inactive }}
                                " 
                                
                                
                                aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                                    <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 2.5-2.5L3 5"/><path d="m3 19 2.5-2.5L3 14"/><path d="M10 6h11"/><path d="M10 12h11"/><path d="M10 18h11"/></svg>
                                    Projects
                                    <svg class="hs-dropdown-open:-rotate-180 md:hs-dropdown-open:rotate-0 duration-300 shrink-0 size-4 ms-auto md:ms-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>

                                <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 relative w-full md:w-52 hidden z-10 top-full ps-7 md:ps-0 md:bg-white md:rounded-lg md:shadow-md before:absolute before:-top-4 before:start-0 before:w-full before:h-5 md:after:hidden after:absolute after:top-1 after:start-[18px] after:w-0.5 after:h-[calc(100%-0.25rem)] after:bg-gray-100 " role="menu" aria-orientation="vertical" aria-labelledby="hs-header-base-dropdown">
                                    <div class="py-1 md:px-1 space-y-0.5">

                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('project create'))
                                        <a class="p-2 md:px-3 flex items-center text-sm {{ request()->routeIs('project.create') 
                                        
                                        ? $active : $inactive }}" 
                                        href="{{ route('project.create') }}">
                                            Submit a Project
                                        </a>
                                        @endif
    
                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('project reviewer list view'))
                                            <a class="p-2 md:px-3  flex items-center text-sm {{ request()->routeIs('project.in_review')  || request()->routeIs('project.review')  
                                            
                                            ? $active : $inactive }}" 
                                            href="{{ route('project.in_review') }}">
                                                Projects for Review    &nbsp;&nbsp;
                                                @php 
                                                    $projects_for_review = 0;
                                                    $projects_for_review = \App\Models\Project::countProjectsForReview(); 
                                                @endphp
                                                <span class="inline-flex items-center py-0.5 px-1.5 rounded-full border border-black text-xs bg-white text-black font-bold"
                                                title="{{ $projects_for_review ? $projects_for_review.' projects to review' : 'No projects to review' }}"
                                                >{{ $projects_for_review }}</span>
                                            </a>
                                        @endif

                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('project update list view'))
                                            <a class="p-2 md:px-3  flex items-center text-sm {{ 
                                            request()->routeIs('project.pending_project_update')  
                                            ? $active : $inactive }}" 
                                            href="{{ route('project.pending_project_update') }}">
                                                Project for Update    &nbsp;&nbsp;
                                                @php 
                                                    $projects_for_review = 0;
                                                    $projects_for_review = \App\Models\Project::countProjectsForUpdate(); 
                                                @endphp
                                                <span class="inline-flex items-center py-0.5 px-1.5 rounded-full border border-black text-xs bg-white text-black font-bold"
                                                title="{{ $projects_for_review ? $projects_for_review.' projects to review' : 'No projects to review' }}"
                                                >{{ $projects_for_review }}</span>
                                            </a>
                                        @endif

                                        @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->can('review list view'))
                                            <a class="p-2 md:px-3  flex items-center text-sm {{ 
                                            request()->routeIs('review.index')  
                                            ? $active : $inactive }}" 
                                            href="{{ route('review.index') }}">
                                                Project Reviews    &nbsp;&nbsp;
                                                @php 
                                                    $projects_reviews = 0;
                                                    $projects_reviews = \App\Models\Review::count(); 
                                                @endphp
                                                <span class="inline-flex items-center py-0.5 px-1.5 rounded-full border border-black text-xs bg-white text-black font-bold"
                                                title="{{ $projects_reviews ? $projects_reviews.' project reviews' : 'No project reviews' }}"
                                                >{{ $projects_reviews }}</span>
                                            </a>
                                        @endif


                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('project list view'))
                                        <a class="p-2 md:px-3 flex items-center text-sm {{ request()->routeIs('project.index') || request()->routeIs('project.edit') ||  request()->routeIs('project.show') ||
                                            request()->routeIs('project.reviewer.index')
                                        ? $active : $inactive }}" 
                                        href="{{ route('project.index') }}">
                                            All Projects &nbsp;&nbsp;
                                            @php 
                                                $projects_count = 0;
                                                $projects_count = \App\Models\Project::countProjects(); 
                                            @endphp
                                            <span class="inline-flex items-center py-0.5 px-1.5 rounded-full border border-black text-xs bg-white text-black font-bold"
                                                title="{{ $projects_count ? $projects_count.' projects' : 'No projects created' }}"
                                                >{{ $projects_count }}</span>
                                        </a>
                                        @endif

                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('reviewer list view'))
                                            <a class="p-2 md:px-3 flex items-center text-sm {{ request()->routeIs('reviewer.index')  ? $active : $inactive }}" 
                                            href="{{ route('reviewer.index') }}">
                                                Project Reviewer
                                            </a>
                                        @endif

                                        

                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('timer list view'))
                                            <a class="p-2 md:px-3 flex items-center text-sm {{ request()->routeIs('project_timer.index')  ? $active : $inactive }}" 
                                            href="{{ route('project_timer.index') }}">
                                                Project Timer
                                            </a>
                                        @endif

                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('document type list view'))
                                            <a class="p-2 md:px-3 flex items-center text-sm {{ request()->routeIs('document_type.index')  ? $active : $inactive }}" 
                                            href="{{ route('document_type.index') }}">
                                                Document Types
                                            </a>
                                        @endif
                                        


                                    </div>
                                </div>
                            </div>
                            <!-- End Projects -->

                            <!-- Dropdown -->
                            <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] [--is-collapse:true] md:[--is-collapse:false] ">

                                @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('DSI God Admin'))
                                <button id="user_manager" type="button" class="hs-dropdown-toggle w-full p-2 flex items-center text-sm  rounded-lg focus:outline-none
                                    @if(request()->routeIs('projects') ||
                                        request()->routeIs('user.index') || request()->routeIs('user.edit') || request()->routeIs('user.create') ||
                                        request()->routeIs('role.index') || request()->routeIs('role.edit') || request()->routeIs('role.create') ||  request()->routeIs('role.add_permissions') ||
                                        request()->routeIs('permission.index') || request()->routeIs('permission.edit') || request()->routeIs('permission.create')
                                        )
                                        {{ $active }}
                                    @else
                                        {{  $inactive  }}
                                    @endif
                                    " aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                                    <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 2.5-2.5L3 5"/><path d="m3 19 2.5-2.5L3 14"/><path d="M10 6h11"/><path d="M10 12h11"/><path d="M10 18h11"/></svg>
                                    User Manager
                                    <svg class="hs-dropdown-open:-rotate-180 md:hs-dropdown-open:rotate-0 duration-300 shrink-0 size-4 ms-auto md:ms-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                </button>
                                @endif
                                <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 relative w-full md:w-52 hidden z-10 top-full ps-7 md:ps-0 md:bg-white md:rounded-lg md:shadow-md before:absolute before:-top-4 before:start-0 before:w-full before:h-5 md:after:hidden after:absolute after:top-1 after:start-[18px] after:w-0.5 after:h-[calc(100%-0.25rem)] after:bg-gray-100 " role="menu" aria-orientation="vertical" aria-labelledby="user_manager">
                                    <div class="py-1 md:px-1 space-y-0.5">

                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('user list view'))
                                        <a class="p-2 md:px-3 flex items-center text-sm {{ request()->routeIs('user.index') || request()->routeIs('user.edit') || request()->routeIs('user.create') ? $active : $inactive }}"
                                        href="{{ route('user.index') }}">
                                            Users
                                        </a>
                                        @endif

                                        
                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('role list view'))
                                        <a class="p-2 md:px-3 flex items-center text-sm {{ request()->routeIs('role.index') || request()->routeIs('role.edit') || request()->routeIs('role.create') || request()->routeIs('role.add_permissions') ? $active : $inactive }}" 
                                        href="{{ route('role.index') }}">
                                            Roles
                                        </a>
                                        @endif

                                        @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('permission list view'))
                                        <a class="p-2 md:px-3 flex items-center text-sm {{ request()->routeIs('permission.index') || request()->routeIs('permission.edit') || request()->routeIs('permission.create') ? $active : $inactive }}"
                                        href="{{ route('permission.index') }}">
                                            Permissions
                                        </a>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <!-- End Dropdown -->

                            
                            @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('activity log list view'))  
                            <a  class="p-2 flex items-center text-sm   rounded-lg {{ request()->routeIs('activity_logs.index') ? $active : $inactive }} "
                                href="{{ route('activity_logs.index') }}" >
                                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Activity Logs
                            </a>
                            @endif

                            {{-- <a  class="p-2 flex items-center text-sm   rounded-lg {{ request()->routeIs('forum.index') ? $active : $inactive }} "
                                href="{{ route('forum.index') }}" >
                                <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                Forum
                            </a> --}}

                             <!-- Dropdown -->
                             <div class="hs-dropdown [--strategy:static] md:[--strategy:fixed] [--adaptive:none] [--is-collapse:true] md:[--is-collapse:false] ">

                                {{-- @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('DSI God Admin')) --}}
                                    <button id="forum_manager" type="button" class="hs-dropdown-toggle w-full p-2 flex items-center text-sm  rounded-lg focus:outline-none
                                        @if(
                                            request()->routeIs('forum.index') || request()->routeIs('forum.edit') || request()->routeIs('forum.create') 
                                            )
                                            {{ $active }}
                                        @else
                                            {{  $inactive  }}
                                        @endif
                                        " aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                                        <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 2.5-2.5L3 5"/><path d="m3 19 2.5-2.5L3 14"/><path d="M10 6h11"/><path d="M10 12h11"/><path d="M10 18h11"/></svg>
                                        Forum
                                        <svg class="hs-dropdown-open:-rotate-180 md:hs-dropdown-open:rotate-0 duration-300 shrink-0 size-4 ms-auto md:ms-1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                    </button>
                                {{-- @endif --}}
                                <div class="hs-dropdown-menu transition-[opacity,margin] duration-[0.1ms] md:duration-[150ms] hs-dropdown-open:opacity-100 opacity-0 relative w-full md:w-52 hidden z-10 top-full ps-7 md:ps-0 md:bg-white md:rounded-lg md:shadow-md before:absolute before:-top-4 before:start-0 before:w-full before:h-5 md:after:hidden after:absolute after:top-1 after:start-[18px] after:w-0.5 after:h-[calc(100%-0.25rem)] after:bg-gray-100 " role="menu" aria-orientation="vertical" aria-labelledby="forum_manager">
                                    <div class="py-1 md:px-1 space-y-0.5">

                                        {{-- @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('user list view')) --}}
                                        <a class="p-2 md:px-3 flex items-center text-sm {{ request()->routeIs('forum.index') || request()->routeIs('forum.edit') || request()->routeIs('forum.create') ? $active : $inactive }}"
                                        href="{{ route('forum.index') }}">
                                            All Forums
                                        </a>
                                        {{-- @endif --}}

                                        <a class="p-2 md:px-3 flex items-center text-sm {{ request()->routeIs('discussion.index') || request()->routeIs('discussion.edit') || request()->routeIs('discussion.create') ? $active : $inactive }}"
                                            href="{{ route('discussion.index') }}">
                                            All Discussions
                                        </a>
 

                                    </div>
                                </div>
                            </div>
                            <!-- End Dropdown -->
                    
                        @endif
                    </div>
                
                </div>

                <div class="my-2 md:my-0 md:mx-2">
                    <div class="w-full h-px md:w-px md:h-4 bg-gray-100 md:bg-gray-300 "></div>
                </div>

                <!-- Button Group -->
                <div class=" flex flex-wrap items-center gap-x-1.5">
                    @if(Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('profile update information'))
                    <a  class="p-2 flex items-center text-sm   rounded-lg {{ request()->routeIs('profile') ? $active : $inactive }} " href="{{ route('profile') }}" >
                        <svg class="shrink-0 size-4 me-3 md:me-2 block md:hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        {{-- Profile --}}
                        {{ Auth::user()->name }}
                    </a>
                    @endif

                </div>
                <!-- End Button Group -->



                <div class="my-2 md:my-0 md:mx-2">
                    <div class="w-full h-px md:w-px md:h-4 bg-gray-100 md:bg-gray-300 "></div>
                </div>

                <!-- Button Group -->
                <div class=" flex flex-wrap items-center gap-x-1.5">
                    <button wire:click="logout" class="py-[7px] px-2.5 inline-flex items-center font-medium text-sm rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " href="#">
                        Logout
                    </button>
                    {{-- <a class="py-2 px-2.5 inline-flex items-center font-medium text-sm rounded-lg bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none " href="#">
                        Get started
                    </a> --}}
                </div>
                <!-- End Button Group -->
            </div>
            </div>
        </div>
        <!-- End Collapse -->
        </nav>
    </header>
    <!-- ========== END HEADER ========== -->


   

</nav>
