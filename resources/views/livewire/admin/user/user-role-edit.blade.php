<!-- Card Section -->
<div class="max-w-full px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    {{-- <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div> --}}

    <form wire:submit="save">
        <!-- Card -->
        <div class="bg-white rounded-xl shadow ">


            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 ">
                    Edit Role for {{ $name }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        <strong>Note:</strong> A user can have multiple roles. However, once a role is assigned, it cannot be removed if there are existing records linked to that role in the account.
                    </p>
                </div>
                <!-- End Col -->

                <!-- Grid -->
                <div class="grid grid-cols-12 gap-x-2  ">
 

                    <div class="space-y-2 col-span-12 sm:col-span-12">
                       
                        <div class="size-auto whitespace-nowrap">
                            <fieldset> 
                                <div class="mt-6 space-y-6">

                                    @if(!empty($roles) && count($roles) > 0)
                                        @foreach ($roles as $role)


                                            @php
                                                $roleHasGlobalAdminPermission = $role->permissions->contains('name', 'system access global admin');
                                                $userHasGlobalAdminPermission = Auth::user()->can('system access global admin');
                                            @endphp

                                            @if ($roleHasGlobalAdminPermission)
                                                @if ($userHasGlobalAdminPermission)
                                                    <div class="flex gap-3">
                                                        <div class="flex h-6 shrink-0 items-center">
                                                            <div class="group grid size-4 grid-cols-1">
                                                                <input 
                                                                id="{{ $role->name }}" 
                                                                type="checkbox" 
                                                                name="selectedRoles" 
                                                                wire:model="selectedRoles"
                                                                  
                                                                value="{{ $role->id }}"
                                                                aria-describedby="{{ $role->name }}-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                                <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25">
                                                                    <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                                                                    <path d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-indeterminate:opacity-100" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="text-sm/6">
                                                            <label for="{{ $role->name }}" class="font-medium text-gray-900">{{ $role->name }}</label>
                                                            <p id="{{ $role->name }}-description" class="text-gray-500 text-wrap">{{ $role->description }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="flex gap-3">
                                                    <div class="flex h-6 shrink-0 items-center">
                                                        <div class="group grid size-4 grid-cols-1">
                                                            <input 
                                                            id="{{ $role->name }}" 
                                                            type="checkbox" 
                                                            name="selectedRoles" 
                                                            wire:model="selectedRoles"
                                                              
                                                            value="{{ $role->id }}"
                                                            aria-describedby="{{ $role->name }}-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                            <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25">
                                                                <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                                                                <path d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-indeterminate:opacity-100" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm/6">
                                                        <label for="{{ $role->name }}" class="font-medium text-gray-900">{{ $role->name }}</label>
                                                        <p id="{{ $role->name }}-description" class="text-gray-500 text-wrap">{{ $role->description }}</p>
                                                    </div>
                                                </div>
                                            @endif

                                                
                                        @endforeach
                                    @endif

                                        
                                </div>
                            </fieldset>
                        </div>

                        
                        @error('selectedRoles')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>




                </div>
                <!-- End Grid -->

                <div class="mt-5 flex justify-center gap-x-2">
                    <a href="{{ route('user.index') }}"
                    wire:navigate
                    class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                        Cancel
                    </a>
                    <button type="submit" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                    Save
                    </button>
                </div>
            </div>
        </div>
        <!-- End Card -->
    </form>

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
                    window.pageUserId = @json(optional(request()->route('user'))->id ?? request()->route('user') ?? null);
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


    
</div>
<!-- End Card Section -->
