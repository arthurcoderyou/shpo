 
<x-app-layout>
     <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard'), ], 
        ['label' => 'Dashboard', 'url' => '#'],
    ]" />

    
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


    <!--  Loaders -->
        <!-- Floating Loading Notification -->
        <div 
            wire:loading 
        class="fixed top-4 right-4 z-50 w-[22rem] max-w-[calc(100vw-2rem)]
                rounded-2xl border border-slate-200 bg-white shadow-lg"
        role="status"
        aria-live="polite"
        >
            <div class="flex items-start gap-3 p-4">
                <!-- Spinner -->
                <svg class="h-5 w-5 mt-0.5 animate-spin text-slate-600 shrink-0"
                    viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z" />
                </svg>

                <!-- Text + Progress -->
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-slate-900">
                        Loading data…
                    </div>
                    <div class="mt-0.5 text-xs text-slate-600">
                        Fetching the latest records. Please wait.
                    </div>

                    <!-- Indeterminate Progress Bar -->
                    <div class="relative mt-3 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div
                        class="absolute inset-y-0 left-0 w-1/3 rounded-full bg-slate-400"
                        style="animation: indeterminate-bar 1.2s ease-in-out infinite;"
                        ></div> 

                    </div>
                </div>
            </div>
        </div>

        {{-- wire:target="save"   --}}
        <div wire:loading  wire:target="save"
        
        >
            <div class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center transition-opacity duration-300">
                <div class="bg-gray-900 text-white px-6 py-5 rounded-xl shadow-xl flex items-center gap-4 animate-pulse w-[320px] max-w-full text-center">
                    <svg class="h-6 w-6 animate-spin text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    <div class="text-sm font-medium">
                        Saving record...
                    </div>
                </div>
            </div>

            
        </div>
    <!--  ./ Loaders -->

    {{-- Do not remove --}}
    {{-- 
        Essential for getting the model id from the browser bar 
        This is to get model id for : 
        1. Full page load (hard refresh, direct URL, normal navigation)
        2. Livewire SPA navigation (wire:navigate)
    --}}
    @push('scripts')
        <script>

            (function () {

                function getData(){
                    window.pageUserId = @json( Auth::id() ?? Auth::id() ?? null);
                    console.log(window.pageUserId);

                    const pageUserId = window.pageUserId; // can be null
                    // 2) Conditionally listen to the model-scoped user channel
                    if (pageUserId) {
                        console.log(`listening to : ${pageUserId}`);
                        window.Echo.private(`user.${pageUserId}`)
                            .listen('.event', (e) => {
                                console.log('[user model-scoped]');

                                let dispatchEvent = `userEvent.${pageUserId}`;
                                Livewire.dispatch(dispatchEvent); 

                                console.log(dispatchEvent);

                                window.location.reload();
                            });
                    }
                }

                /**
                 * 1. Full page load (hard refresh, direct URL, normal navigation)
                 */
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', () => {
                        getData();
                    });
                } else {
                    // DOM already loaded
                    getData();
                }

                /**
                 * 2. Livewire SPA navigation (wire:navigate)
                 */
                document.addEventListener('livewire:navigated', () => {
                    getData();
                });

            })();
 


        </script>
    @endpush

    
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        window.addEventListener('systemEvent', () => {
            window.location.reload();
        });




    </script>
</x-app-layout>
