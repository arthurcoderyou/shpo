<!-- Card Section -->
<div class="max-w-full px-4 py-6 sm:px-6 lg:px-8  mx-auto"
    x-data="{
        showModal: false,  
        handleKeydown(event) {
            if (event.keyCode == 191) {
                this.showModal = true; 
            }
            if (event.keyCode == 27) {
                this.showModal = false; 
                $wire.search = '';
            }

        },
    }"
>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initMap"
        async
        defer>
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js"></script>




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
                    Edit User
                    </h2>
                </div>
                <!-- End Col -->

                <!-- Grid -->
                <div class="grid grid-cols-12 gap-2 ">

                    <div class="col-span-12 sm:col-span-6">
                         
                        <x-ui.input 
                            id="name"
                            name="name"
                            type="text"
                            wire:model.live="name"   
                            label="Name"
                            required  
                            placeholder="Enter name" 
                            :error="$errors->first('name')"
 

                           xInit="$nextTick(() => $el.focus())"
                        />

                    </div>

                    

                    <div class="col-span-12 sm:col-span-6">
                        <x-ui.input 
                            id="email"
                            name="email"
                            type="email"
                            wire:model.live="email"   
                            label="Email address"
                            required  
                            placeholder="Enter email" 
                            :error="$errors->first('email')"
  
                        />
                    </div>

                    <div class="col-span-12 sm:col-span-4">

                        <x-input-label for="address" :value="__('Address')" />
                        <div class="relative mt-1 flex rounded-md shadow-sm">
                            <!-- Prefix Button -->
                            <button
                                @click="showModal = true, initMap" type="button"
                                @keydown.window="handleKeydown" 

                                type="button"
                                class="inline-flex items-center rounded-l-md border border border-gray-300 bg-gray-50 px-3 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                aria-label="Open map"
                            >
                                <!-- Globe Icon -->
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 3c4.97 0 9 4.03 9 9s-4.03 9-9 9-9-4.03-9-9 4.03-9 9-9zm0 0c2.5 2.5 2.5 13.5 0 18m0-18c-2.5 2.5-2.5 13.5 0 18m-9-9h18" />
                                </svg>
                            </button>

                            <!-- Address Input -->
                            <x-text-input
                                @click="showModal = true, initMap" type="button"
                                @keydown.window="handleKeydown" 

                                readonly
                                wire:model="address"
                                id="address"
                                name="address"
                                type="text"
                                class="block w-full rounded-l-none"
                                required
                                autocomplete="address"
                            />
                        </div>

                    </div>

                    <div class="col-span-12 sm:col-span-4">
                         
                       
                        <x-inputs.text-dropdown
                            name="company"
                            label="Company"
                            :value="$company"
                            placeholder="Search or select company..."
                            :options="$companies"    
                            wire:model.live="company"       
                        />

                        <x-input-error class="mt-2" :messages="$errors->get('company')" />
            


                    </div>

                    <div class="col-span-12 sm:col-span-4">
                         
                        <x-input-label for="phone_number" :value="__('Phone number')" />
 
                        <div wire:ignore>
                            <input id="phone" type="tel"
                                class="w-full block rounded-lg border-slate-300" />
                        </div> 


                        <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone_number_country_code')" />

                    </div>


                    

                    <div class="col-span-12 sm:col-span-6">

                        @php 
                            $password_rules = [
                                'Minimum Length' => 'At least 8 characters long.',
                                'Letters'        => 'Must contain both uppercase and lowercase letters.',
                                'Numbers'        => 'Must include at least one numeric digit.',
                                'Symbols'        => 'Must include at least one special symbol (e.g., ! @ # $ % ^ & *).',
                                'Security'       => 'Avoid using common words or easily guessed patterns.',
                            ];
                        @endphp
                        <x-ui.password-input 
                            id="password"
                            name="password"
                            type="password"
                            wire:model.live="password"   
                            label="Change Password"
                            placeholder="Enter changed password" 
                            :error="$errors->first('password')"

                            displayTooltip
                            position="top"
                            tooltipText="Your changed password must meet all security rules below:" 
                            :tooltipLabelTextArrMsg="$password_rules"
 
                            :show-password-toggle="true"
                        /> 

 
                    </div>

                    <div class="col-span-12 sm:col-span-6">
  

                       <x-ui.password-input 
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            wire:model.live="password_confirmation"   
                            label="Confirm changed password"
                            placeholder="Re-enter changed password" 
                            :error="$errors->first('password_confirmation')"

                            displayTooltip
                            position="top"
                            tooltipText="Please re-enter your changed password to confirm it matches."
                            :tooltipLabelTextArrMsg="[
                                'Match Required' => 'This must be exactly the same as the password you entered above.',
                                'No Copy Errors' => 'Check for missing letters, capitalization, or added spaces.',
                            ]"
 
                            :show-password-toggle="true"
                        /> 
                    </div>

                    {{-- 
                    <div class="col-span-12 sm:col-span-12">
                        <label for="role" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Role
                        </label>

                         


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
                    --}}


                </div>
                <!-- End Grid -->

                <div class="mt-5 flex justify-center gap-x-2">
                    <x-ui.button 
                        id="cancel" 
                        label="Cancel"
                        sr="Cancel" 
                        :linkHref="route('user.index')" {{-- to make it as a link --}}

                        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none"
                    />
  
                    <x-ui.button 
                        id="save" 
                        type="button"
                        label="Save"
                        sr="Save"
 
                        onclick="confirm('Are you sure, you want to save this record?') || event.stopImmediatePropagation()" 
                        wire:click.prevent="save" 


                        class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                    />

                    
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



    
    

     <!-- Search location modal-->
    @teleport('body')
        <div x-show="showModal" x-trap="showModal" class="relative z-50 " aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <!-- <div class="fixed inset-0 z-10 w-screen overflow-y-auto py-10"> -->
            <div class="fixed inset-0 z-50 w-screen overflow-y-auto py-10">
                <div class="flex justify-center p-4 sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-7xl">
                        <div 
                        {{-- @click.outside="showModal = false"  --}}
                        class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="w-full px-1 pt-1" x-data="{
                                searchPosts(event) {
                                    document.getElementById('searchInput').focus();
                                    event.preventDefault();
                                }
                            }">
                                {{-- <form action="" autocomplete="off">
                                    <input
                                    autocomplete="off"
                                    wire:model.live.throttle.500ms="location_search" type="text" id="searchInput"
                                    name="searchInput"
                                    class="block w-full flex-1 py-2 px-3 mt-2 outline-none border-none rounded-md bg-slate-100"
                                    placeholder="Search for project name ..." @keydown.slash.window="searchPosts" />
                                </form> --}}
                                <div class="mt-2 w-full overflow-hidden rounded-md bg-white">

                                     
                                    <div class=" py-2 px-3 border-b border-slate-200 text-sm font-medium text-slate-500">

                                        Search map <strong>(Click to OK 
                                            {{-- or out of the box  --}}
                                            to close dialog
                                            )</strong>

                                    </div>

                                    
                                    <div class="  py-2 px-3 hover:bg-slate-100 bg-white border border-gray-200 shadow-sm rounded-xl mb-1"
                                    {{-- wire:click="saerch_project('{{  $result->id }}')"
                                    @click="showModal = false" --}}
                                    >
                                         

                                        <div>
                                            <input
                                            autofocus autocomplete="location"
                                            wire:model.live="location"
                                            placeholder="Search location"
                                            id="search-box" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                        
                                            <input type="hidden" id="latitude" wire:model.live="latitude">
                                            <input type="hidden" id="longitude" wire:model.live="longitude">
                                        </div>


                                        <div class="max-w-full text-wrap ">
                                            <div>
                            
                                                <div id="map" style="height: 500px; width: 100%;" wire:ignore></div>
                                            {{-- <button wire:click="saveLocation">Save Location</button> --}}
                                            </div>
                    
                    
                                            <div>
                                                @error('location')
                                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                                @enderror
                    
                                            </div>





                                        </div>

                                            
                                        <button  type="button"
                                            @click="showModal = false" type="button"
                                            @keydown.window="handleKeydown"
                                            class="mt-1 py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                            OK
                                        </button> 

                                         
                                    </div>
                                             

                                         

    

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endteleport
    <!-- ./ Search location modal-->



    {{-- - script to run the geolocation --}}
    <script>
        let map, marker, searchBox;

       


        function initMap() {


            // check if the user has address already 
            const livewireAddress = @js($this->address);

            // check if the user has address already  
 

            const defaultCenter = { lat: 13.4443, lng: 144.7937 }; // Fallback (Guam)

            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultCenter,
                zoom: 11,
                // mapTypeId: google.maps.MapTypeId.SATELLITE // ✅ Default to Satellite view
            });

            marker = new google.maps.Marker({
                position: map.getCenter(),
                map: map,
                draggable: true
            });

            const input = document.getElementById("search-box");
            searchBox = new google.maps.places.SearchBox(input);


            if (livewireAddress) {
                input.value = livewireAddress;
            }

            if (livewireAddress) {
                const geocoder = new google.maps.Geocoder();

                geocoder.geocode({ address: livewireAddress }, (results, status) => {
                    if (status === "OK" && results[0]) {
                        map.setCenter(results[0].geometry.location);
                        marker.setPosition(results[0].geometry.location);
                    }
                });


            }else{



                // =========================   
                // 📍 Use Browser Geolocation
                // =========================
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const currentLocation = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            };

                            map.setCenter(currentLocation);
                            marker.setPosition(currentLocation);

                            // 🔄 Reverse geocode immediately
                            geocoder.geocode(
                                { location: currentLocation },
                                function (results, status) {
                                    if (status === "OK" && results[0]) {
                                        const locationName = results[0].formatted_address;

                                        // ✅ Set default Livewire address
                                        @this.set('address', locationName);

                                        // ✅ Update the search box text
                                        input.value = locationName;


                                        console.log("Initial address set:", locationName);
                                    } else {
                                        console.error("Initial geocoder failed:", status);
                                    }
                                }
                            );
    

                        },
                        (error) => {
                            console.warn('Geolocation denied or unavailable:', error.message);
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0,
                        }
                    );
                }



            }


           
 

            searchBox.addListener("places_changed", function () {
                let places = searchBox.getPlaces();
                if (places.length == 0) return;

                let place = places[0];
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);

                let fullAddress = place.formatted_address || place.name; // Use full address if available

                @this.set('address', fullAddress); // ✅ Use full address instead of just name

                console.log("Updated Location:", fullAddress);
            });


            const geocoder = new google.maps.Geocoder();

            marker.addListener("dragend", function () {
                let lat = marker.getPosition().lat();
                let lng = marker.getPosition().lng();

                // Reverse geocode to get location name
                geocoder.geocode({ location: { lat, lng } }, function (results, status) {
                    if (status === "OK" && results[0]) {
                        let locationName = results[0].formatted_address;

                        // Send data to Livewire 
                        @this.set('address', locationName);

                        // ✅ Update the search box text
                        input.value = locationName;

                        console.log("Updated address:", locationName);
                    } else {
                        console.error("Geocoder failed: " + status);
                    }
                });
            });
        }

        window.onload = initMap;

         
    </script>
    {{-- ./ - script to run the geolocation --}}


    <script>
    document.addEventListener('livewire:navigated', initPhone);
    document.addEventListener('DOMContentLoaded', initPhone);

    function initPhone() {

        // Existing Livewire values (if present)
        const savedIso2 = (@js($this->phone_number_country_code ?? null) || null);
        const savedE164 = @js($this->phone_number ?? null);


        const input = document.querySelector('#phone');
        if (!input || input.dataset.ready) return;
        input.dataset.ready = "1";
 

        const iti = window.intlTelInput(input, { 
            // If user has saved ISO2, prefer it. Otherwise auto-detect by IP.
            initialCountry: savedIso2 ? savedIso2.toLowerCase() : "auto",

            geoIpLookup: function (callback) {
               

                // Example provider: ipapi.co (simple). You can swap providers.
                fetch('https://ipapi.co/json/')
                    .then(res => res.json())
                    .then(data => {
                        const iso2 = (data && data.country_code) ? data.country_code.toLowerCase() : 'ph';
                        callback(iso2);
                    })
                    .catch(() => callback('ph'));
            },

            separateDialCode: false,
            nationalMode: false,
            autoPlaceholder: "polite",
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/utils.js",
        });

        
        // Apply saved number after init (if any)
        if (savedE164) {
            iti.setNumber(savedE164);
        }

        // Sync initial state to Livewire once (after plugin is ready)
        setTimeout(syncToLivewire, 0);

        function syncToLivewire() {
            const data = iti.getSelectedCountryData() || {};
            const iso2 = (data.iso2 || '').toUpperCase() || null;

            let e164 = null;
            try {
                e164 = iti.getNumber(window.intlTelInputUtils.numberFormat.E164);
            } catch (e) {
                e164 = null;
            }

            if (!input.value || input.value.trim() === '') e164 = null;

            @this.set('phone_number_country_code', iso2);
            @this.set('phone_number', e164);
        }

        input.addEventListener('countrychange', syncToLivewire);
        input.addEventListener('blur', syncToLivewire);

        let t = null;
        input.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(syncToLivewire, 250);
        });
    }
    </script>



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
