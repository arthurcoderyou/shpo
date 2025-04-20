<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto grid grid-cols-12 gap-x-2">
    {{-- The whole world belongs to you. --}}
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA70BOfcc1ELmwAEmY-rFNkbNauIXT79cA&libraries=places"></script>
     
    {{-- <div wire:loading class="loading-overlay"> --}}
        <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    {{-- </div> --}}
    

    <form class="col-span-12  sm:col-span-10" wire:submit="save">
        <!-- Card -->
        <div class="bg-white rounded-xl shadow ">


            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 ">
                    Submit new project
                    </h2>
                </div>
                <!-- End Col -->

                <div class="grid grid-cols-12 gap-x-2  ">

                    <div class="space-y-2 col-span-12 my-1  ">
                        <h2>Search and Save Location</h2>
                        {{-- <input type="text" id="search-box"  wire:model="location"> --}}
                        {{-- <input type="text" id="search-box" placeholder="Search location" wire:model="location"> --}}
    
     
                        {{-- <input
                        autofocus autocomplete="location"
                        wire:model="location"
                        readonly placeholder="Search location"
                        id="search-box" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm text-gray-500 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder=""> --}}

                        
                        <input
                        autofocus autocomplete="location"
                        wire:model="location"
                          placeholder="Search location"
                        id="search-box" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

    
                        <input type="hidden" id="latitude" wire:model.live="latitude">
                        <input type="hidden" id="longitude" wire:model.live="longitude">


                         
                    </div>
                    <div class="space-y-2 col-span-12    ">
                        <div>
                            
                            <div id="map" style="height: 500px; width: 100%;" wire:ignore></div>
                        {{-- <button wire:click="saveLocation">Save Location</button> --}}
                        </div>


                        <div>
                            @error('location')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror

                            @error('latitude')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror

                            @error('longitude')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>





                </div>


 
                



                <!-- Grid -->
                <div class="grid grid-cols-12 gap-x-2  ">

                    <div class="space-y-2 col-span-12 sm:col-span-4  ">
                        <label for="name" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Name
                        </label>

                        <input
                        autofocus autocomplete="name"
                        wire:model="name"
                        id="name" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                        @error('name')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-4  ">
                        <label for="federal_agency" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Company
                        </label>

                        <input
                        autofocus autocomplete="federal_agency"
                        wire:model="federal_agency"
                        id="federal_agency" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                        @error('federal_agency')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>


                    <div class="space-y-2 col-span-12 sm:col-span-4  ">
                        <label for="type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Type
                        </label>


                        <select
                        autocomplete="type"
                        wire:model="type"
                        id="type"
                        class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  ">
                            <option selected="">Select type</option>

                            @if(!empty($project_types))
                                @foreach ($project_types as $type_id => $type_name )
                                    <option>{{ $type_name }}</option> 
                                @endforeach 
                            @endif
                        </select>

                        {{-- <input
                        autofocus autocomplete="type"
                        wire:model="type"
                        id="type" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder=""> --}}

                        @error('type')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>






                    <div class="space-y-2 col-span-12   ">
                        <label for="description" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Description
                        </label>

                        <textarea
                        autofocus autocomplete="description"
                        wire:model="description"
                        id="description"  class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder=""></textarea>

                        @error('description')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>
                    
                    {{-- 
                    <div class="space-y-2 col-span-12     ">
                        <label for="description" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Attachments
                        </label>

                        <livewire:dropzone
                            wire:model="attachments"
                            :rules="['file', 'mimes:png,jpeg,jpg,pdf,docx,xlsx,csv,txt,zip', 'max:20480']"
                            :multiple="true" />


                        @error('attachments')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror 

                    </div>
                    --}}

                     

                    <!-- Dynamic Project Documents Section -->
                    <div class="space-y-2 col-span-12  mt-5">
                        @foreach($projectDocuments as $index => $document)
                            <div class="border border-black p-2 rounded-md mb-4">
                                <label class="inline-block text-sm font-medium text-gray-800 ">
                                    Submission Type
                                </label>
                                <select
                                    class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "
                                    wire:model.live="projectDocuments.{{ $index }}.document_type_id"
                                    
                                    >
                                    <option value="">Select Submission Type</option>
                                    @foreach($documentTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach

                                    


                                </select>

                                @error("projectDocuments.".$index.".document_type_id")
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror

                                <label class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                    Document Attachments ( Only PNG, JPEG, JPG, PDF, DOCX, XLSX, CSV, TXT, and ZIP files are allowed. )
                                </label>
                                <input class="block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none   file:bg-gray-50 file:border-0
                                file:me-4
                                file:py-3 file:px-4
                                "
                                type="file" wire:model.live="projectDocuments.{{ $index }}.attachments" multiple>

                                @error("projectDocuments.".$index.".attachments")
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror

                                

                                <!-- Show Selected Files Before Upload -->
                                <div class="my-2">
                                    @if(isset($projectDocuments[$index]['attachments']) && count($projectDocuments[$index]['attachments']) > 0) 
                                        <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                            Selected Files: (Choose files again to change the uploaded list)
                                        </p>
                                        @foreach($projectDocuments[$index]['attachments'] as $file)
                                            <div class="w-full mb-2 ">
                                                <span class="block py-3 px-4 text-sm font-medium bg-white border border-gray-200 text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg  ">{{ $file->getClientOriginalName() }}</span>
                                            </div>
                                        @endforeach



                                    @endif
                                </div>

                                <!-- Remove Button -->
                                {{-- <button type="button" wire:click="removeProjectDocument({{ $index }})" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Remove
                                </button> --}}
                                
                            </div>
                        @endforeach
                        {{-- <div class="mb-2">
                            <button class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" type="button" wire:click="addProjectDocument">+ Add Document</button>
                        </div> --}}

                    </div>  

                    

                    


                </div> 


                <!-- section visible to admin only -->
                {{-- @if( Auth::user()->hasRole('DSI God Admin') && Auth::user()->hasPermissionTo('timer edit') )  --}}
                <!-- Timer -->
                <div class="grid grid-cols-12 gap-x-2  
                {{ ( Auth::user()->hasRole('DSI God Admin') || Auth::user()->hasRole('Reviewer') )  ? 'block' : 'hidden'}}
                ">

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="submitter_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Submitter duration
                        </label>

                        <input
                        min="1"
                        autofocus autocomplete="submitter_response_duration"
                        wire:model.live="submitter_response_duration"
                         
                        id="submitter_response_duration" type="number" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                        @error('submitter_response_duration')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="submitter_response_duration_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Submitter duration type
                        </label>

                        <select 
                         
                        autofocus autocomplete="submitter_response_duration_type"
                        wire:model.live="submitter_response_duration_type"
                        id="submitter_response_duration_type" 
                        class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  ">
                            <option selected="">Select type</option>
                            <option value="day">Day</option>
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                        </select>

                        @error('submitter_response_duration_type')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-6  ">
                        <label for="submitter_due_date" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Submitter response due date
                        </label>

                        <input readonly 
                        {{-- autofocus autocomplete="submitter_due_date"
                        wire:model.live="submitter_due_date" --}}
                        value="{{ \Carbon\Carbon::parse($submitter_due_date)->format('d M, h:i A') }}"
                        id="submitter_due_date" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                        @error('submitter_due_date')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                </div>

                <!-- Recieved time or response is determined by the submitted -->
                <div class="grid grid-cols-12 gap-x-2  
                {{ ( Auth::user()->hasRole('DSI God Admin')  || Auth::user()->hasRole('Reviewer') 

                )  ? 'block' : 'hidden'
                
                
                }}
                ">

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="reviewer_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Reviewer duration
                        </label>

                        <input
                        min="1"
                        autofocus autocomplete="reviewer_response_duration"
                        wire:model.live="reviewer_response_duration"
                        id="reviewer_response_duration" type="number" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                        @error('reviewer_response_duration')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="reviewer_response_duration_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Reviewer duration type
                        </label>

                        <select 
                        autofocus autocomplete="reviewer_response_duration_type"
                        wire:model.live="reviewer_response_duration_type"
                        id="reviewer_response_duration_type" 
                        class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  ">
                            <option selected="">Select type</option>
                            <option value="day">Day</option>
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                        </select>

                        @error('reviewer_response_duration_type')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>


                    


                    <div class="space-y-2 col-span-12 sm:col-span-6  ">
                        <label for="reviewer_due_date" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Reviewer response due date
                        </label>

                        <input readonly 
                        {{-- autocomplete="reviewer_due_date"
                        wire:model.live="reviewer_due_date" --}}    
                        value="{{ \Carbon\Carbon::parse($reviewer_due_date)->format('d M, h:i A') }}"
                        id="reviewer_due_date" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                        @error('reviewer_due_date')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    
                    
                    @if(!empty($project_timer))
                    <p class="text-sm text-gray-500 mt-2 col-span-12"> 
                        Updated {{  \Carbon\Carbon::parse($project_timer->updated_at)->format('d M, h:i A') }} by {{ $project_timer->updator ? $project_timer->updator->name : '' }}
                                            
                    </p>

                    @else
                    <p class="text-sm text-gray-500 mt-2 col-span-12"> 
                        Project timers are set to default and haven't been updated.                   
                    </p>
                    @endif


                </div>
                <!-- End Timer -->
                {{-- @endif --}}


                <!-- -->

                @if(Auth::user()->hasRole('User')) 
                <!-- End Grid -->
                <p class="text-sm text-gray-600 mt-2">{{ !empty($reviewer_due_date) ? 'Expect to get a review at '.\Carbon\Carbon::parse($reviewer_due_date)->format('d M, h:i A') : '' }}</p>
                @endif

                @if ($errors->any())
                         
                    @foreach ($errors->all() as $error) 


                        <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4 " role="alert" tabindex="-1" aria-labelledby="hs-soft-color-danger-label">
                            <span id="hs-soft-color-danger-label" class="font-bold">Error: </span>
                            {{ $error }}
                        </div>


                    @endforeach 
                @endif 


                <div class="mt-5 flex justify-center gap-x-2">
                    <a href="{{ route('project.index') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                        Cancel
                    </a>
                    <button type="submit" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                        Save Draft
                    </button>

                    @if( Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('project submit') )
                        <button  type="button"
                            onclick="confirm('Are you sure, you want to submit this project?') || event.stopImmediatePropagation()"
                            wire:click.prevent="submit_project()"
                            
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-sky-600 text-white hover:bg-sky-700 focus:outline-none focus:bg-sky-700 disabled:opacity-50 disabled:pointer-events-none">
                            Submit
                        </button> 
                    @endif
                </div>
            </div>
        </div>
        <!-- End Card -->
    </form>

    <aside class="col-span-12 md:col-span-2 mt-2 md:mt-0">
        <div class="bg-white rounded-xl shadow  ">
            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 ">
                        Project Subscribers  
                    </h2>
                    <p class="text-gray-500 text-xs">Users that will be notified on project updates</p>
                </div> 

                <label for="name" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                    Search for User Name
                </label>

                <input type="text" wire:model.live="query" placeholder="Type to search..." class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  ">
                
                <!-- Search Results Dropdown -->
                @if(!empty($users))
                    <ul class="border rounded mt-2 bg-white w-full max-h-48 overflow-auto">
                        @foreach($users as $user)
                            <li wire:click="addSubscriber({{ $user->id }})" class="p-2 cursor-pointer hover:bg-gray-200">
                                {{ $user->name }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            
                <!-- Selected Subscribers -->
                <div class="mt-4">
                    <h3 class="font-bold">Selected Subscribers:</h3>
                    
                    @if(!empty($selectedUsers))
                        <ul>
                            @foreach($selectedUsers as $index => $user)
                                <li class="flex items-center justify-between bg-gray-100 p-2 rounded mt-1 text-truncate">
                                    <span>{{ $user['name'] }}</span>
                                    <button wire:click="removeSubscriber({{ $index }})" class="text-red-500">❌</button>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">No subscribers selected.</p>
                    @endif
                </div>

            </div>

        </div>
    </aside>

    <script>
        let map, marker, searchBox;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 13.4443, lng: 144.7937 }, // Centered on Guam
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
            // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // searchBox.addListener("places_changed", function () {
            //     let places = searchBox.getPlaces();
            //     if (places.length == 0) return;

            //     let place = places[0];
            //     map.setCenter(place.geometry.location);
            //     marker.setPosition(place.geometry.location);


            //     @this.set('latitude', place.geometry.location.lat());
            //     @this.set('longitude', place.geometry.location.lng());
            //     @this.set('location', place.name);

                 

            // });

            searchBox.addListener("places_changed", function () {
                let places = searchBox.getPlaces();
                if (places.length == 0) return;

                let place = places[0];
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);

                let fullAddress = place.formatted_address || place.name; // Use full address if available

                @this.set('latitude', place.geometry.location.lat());
                @this.set('longitude', place.geometry.location.lng());
                @this.set('location', fullAddress); // ✅ Use full address instead of just name

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
                        @this.set('latitude', lat);
                        @this.set('longitude', lng);
                        @this.set('location', locationName);

                        console.log("Updated Location:", locationName);
                    } else {
                        console.error("Geocoder failed: " + status);
                    }
                });
            });
        }

        window.onload = initMap;

         
    </script>

    

</div>
<!-- End Card Section -->
