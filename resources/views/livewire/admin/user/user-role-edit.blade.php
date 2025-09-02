<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

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
                                                                id="comments" 
                                                                type="checkbox" 
                                                                name="selectedRoles" 
                                                                wire:model="selectedRoles"
                                                                  
                                                                value="{{ $role->id }}"
                                                                aria-describedby="comments-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                                <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25">
                                                                    <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                                                                    <path d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-indeterminate:opacity-100" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <div class="text-sm/6">
                                                            <label for="comments" class="font-medium text-gray-900">{{ $role->name }}</label>
                                                            <p id="comments-description" class="text-gray-500 text-wrap">{{ $role->description }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="flex gap-3">
                                                    <div class="flex h-6 shrink-0 items-center">
                                                        <div class="group grid size-4 grid-cols-1">
                                                            <input 
                                                            id="comments" 
                                                            type="checkbox" 
                                                            name="selectedRoles" 
                                                            wire:model="selectedRoles"
                                                              
                                                            value="{{ $role->id }}"
                                                            aria-describedby="comments-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto" />
                                                            <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25">
                                                                <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                                                                <path d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-indeterminate:opacity-100" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="text-sm/6">
                                                        <label for="comments" class="font-medium text-gray-900">{{ $role->name }}</label>
                                                        <p id="comments-description" class="text-gray-500 text-wrap">{{ $role->description }}</p>
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

    
</div>
<!-- End Card Section -->
