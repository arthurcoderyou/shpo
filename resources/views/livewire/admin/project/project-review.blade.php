<?php 
    function isImageMime($filename) {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($extension, $imageExtensions);
    }
?>

<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto grid grid-cols-12 gap-x-2">

    {{-- The whole world belongs to you. --}}
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA70BOfcc1ELmwAEmY-rFNkbNauIXT79cA&libraries=places"></script>


    <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div>


    
    <!-- Card -->
    <div class="bg-white rounded-xl shadow  col-span-12 ">
        <div class="p-4">

            <!-- Profile -->
            <div class="flex items-center ">
                
            
                <div class="grow">

                    <h1 class="text-xl font-medium text-sky-800 ">
                        Project Review
                    </h1>

                    <h1 class="text-lg font-medium text-gray-800 ">
                        Project Review on {{ $name }}
                    </h1>
                    <p class="text-sm text-gray-600 ">
                        {{ $federal_agency }}
                    </p>

                    <p class="text-sm text-blue-600  ">
                        {{ $type }} 
                    </p>

                    @if(!Auth::user()->hasRole('User'))
                        <p class="text-sm text-green-600 ">
                            Project #: {{ !empty($project_number) ? $project_number : 'NOT SET' }}
                        </p>
                        <p class="text-sm text-yellow-600 ">
                            SHPO #: {{ !empty($shpo_number) ? $shpo_number  : 'NOT SET' }}
                        </p>
                    @endif

                    
                </div>


                <div>
                    <p class="text-sm text-gray-600 ">
                        Project Status: <strong>{!! $status !!}</strong>
                    </p>
                    {{-- <p class="text-sm text-gray-600 ">
                        Review Status: <strong>{{ ucfirst($project->getReview()->review_status) }}</strong>
                    </p> --}}


                    @if($project->status !== "approved")
                        <p class="text-sm text-gray-600 ">
                            Review Status: 
                            @if($project->getReview()->review_status == "approved")
                                <span class="font-bold text-lime-500">{{ ucfirst($project->getReview()->review_status) }} </span> 
                            @elseif($project->getReview()->review_status == "rejected")
                                <span class="font-bold text-red-500">{{ ucfirst($project->getReview()->review_status) }} </span> 

                            @else 
                                <span class="font-bold text-yellow-500">{{ ucfirst($project->getReview()->review_status) }} </span> 

                            @endif
                        </p>
                        <hr>
                        <p class="text-sm text-gray-600 ">
                            Review Schedule: 

                            @if (\Carbon\Carbon::parse($project->reviewer_due_date)->isPast())
                                <span class="font-bold text-red-500">Overdue</span>
                            @else
                                <span class="font-bold text-lime-500">On Time</span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-600 ">
                            Expected review on or before <br>  
                            <strong>
                                {{ \Carbon\Carbon::parse($project->reviewer_due_date)->format('M d, Y h:i A') }}
                            </strong>
                            
                        </p>
                    @endif



                </div>



            </div>
            <!-- End Profile -->



            <div class="grid grid-cols-12 gap-x-2  ">

                <div class="space-y-2 col-span-12 my-1  ">

                    <!-- Map -->
                    <div class="flex-row md:flex items-center justify-between my-1 ">
                        
                    
                            
                        <div>
                            <p class="text-sm text-gray-600 ">
                                <strong>Location:</strong> {!! $location !!}
                            </p> 

                        </div>
                        <div>
                            <p class="text-sm text-gray-600 ">
                                <strong>Latitude:</strong> {!! $latitude !!}
                            </p> 

                        </div>

                        <div>
                            <p class="text-sm text-gray-600 ">
                                <strong>Longitude:</strong> {!! $longitude !!}
                            </p> 

                        </div>



                    </div>
                    <!-- End Map -->


                </div>


                <div class="space-y-2 col-span-12    ">
                    <div>
                        
                        <div id="map" style="height: 500px; width: 100%;" wire:ignore></div>
                    {{-- <button wire:click="saveLocation">Save Location</button> --}}
                    </div>


                        
                </div>

            </div>


            
            <!-- About -->
            <div class="mt-4">
                <p class="text-sm text-gray-600 ">
                    {{ $description }}
                </p>
                    
                

                @if(isset($existingFiles) && count($existingFiles) > 0)
                <div class="border border-blue-200 rounded-lg p-2 my-2.5">


                    <label for="description" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                        Attachments
                    </label>

                    @php($index = 1)

                    @foreach($existingFiles as $date => $project_documents)
                        

                        <div class="hs-accordion-group"  >

                            <div class="hs-accordion  " id="attachment-{{ $index }}">
                                <button type="button" class="hs-accordion-toggle   
                                @if( $index == 1) active @endif hs-accordion-active:text-blue-600 py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none " aria-expanded="false" aria-controls="hs-basic-collapse-{{ $index }}">

                                    <svg class="hs-accordion-active:hidden block  size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5v14"></path>
                                    </svg>

                                    <svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"></path>
                                    </svg>
                                    {{ $date }}
                                </button>


                                

                                <div id="hs-basic-collapse-{{ $index }}" class="hs-accordion-content 
                                @if( $index !== 1) hidden @endif
                                w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="attachment-{{ $index }}">
                                

                                    <div class="dz-flex dz-flex-wrap dz-gap-x-10 dz-gap-y-2 dz-justify-start dz-w-full  ">
                                        @foreach($project_documents as $project_document )

                                            <?php
                                                $document = \App\Models\ProjectDocument::find($project_document['id']);
                                            ?>


                                            @if(!empty($document ))
                                                <div class="border border-black p-2 rounded-md mb-4 w-full">
                                                    <label class="inline-block text-sm font-medium text-gray-800  mb-1">
                                                        {{ $document->document_type->name }}
                                                    </label> 

                                                    @foreach ($document->project_attachments as $attachment)
                                                        
                                                        <div class="dz-flex dz-items-center dz-justify-between dz-gap-2 dz-border dz-rounded dz-border-gray-200 dz-w-full dz-h-auto dz-overflow-hidden ">
                                                            <div class="dz-flex dz-items-center dz-gap-3">

                                                                <?php 
                                                                    $attachment_file = asset('storage/uploads/project_attachments/' . $attachment->attachment);
                                                                ?>


                                                                @if(isImageMime($attachment_file))
                                                                    <div class="dz-flex-none dz-w-14 dz-h-14">
                                                                        <img src="{{ $attachment_file  }}" class="dz-object-fill dz-w-full dz-h-full" alt="{{ $attachment_file  }}">
                                                                    </div>
                                                                @else
                                                                    <div class="dz-flex dz-justify-center dz-items-center dz-w-14 dz-h-14 dz-bg-gray-100 ">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="dz-w-8 dz-h-8 dz-text-gray-500">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                                        </svg>
                                                                    </div>
                                                                @endif
                                                                <div class="dz-flex dz-flex-col dz-items-start dz-gap-1">
                                                                    <div class="dz-text-center dz-text-slate-900 dz-text-sm dz-font-medium ">{{ $attachment->attachment }}</div>
                                                                    </div>
                                                            </div>

                                                            <div class="dz-flex dz-items-center dz-mr-3">
                                                                <a href="{{ $attachment_file }}" download="{{ $attachment->attachment }}"
                                                                class="inline"
                                                                >   
                                                                    

                                                                    <svg class="size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 242.7-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7 288 32zM64 352c-35.3 0-64 28.7-64 64l0 32c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-32c0-35.3-28.7-64-64-64l-101.5 0-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352 64 352zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z"/></svg>
                                                                </a>
                                                            </div>


                                                            {{-- @if(Auth::user()->hasRole('Admin'))
                                                                <div class="dz-flex dz-items-center dz-mr-3">
                                                                    <button type="button" 
                        
                                                                    onclick="confirm('Are you sure, you want to remove this attachment?') || event.stopImmediatePropagation()"
                                                                    wire:click.prevent="removeUploadedAttachment({{ $file['id'] }})"
                        
                                                                    
                                                                    >   
                                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="dz-w-6 dz-h-6 dz-text-black ">
                                                                            <path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 011.06 0L12 10.94l5.47-5.47a.75.75 0 111.06 1.06L13.06 12l5.47 5.47a.75.75 0 11-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 01-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                                                        </svg>
                                                                        
                                                                    </button>
                                                                </div>
                                                            @endif --}}

                                                        </div>
                                                        
                                                    @endforeach
                                                </div>


                                            @endif
                                            

                                        @endforeach
                                    </div>



                                </div>
                            </div>

                            
                        
                            
                        </div>
                        @php($index++)

                    @endforeach

                </div>
                @endif


                <ul class="mt-5 flex flex-col gap-y-3">
                        
                    
                    <li class="flex items-center gap-x-2.5">
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M304 128a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM49.3 464l349.5 0c-8.9-63.3-63.3-112-129-112l-91.4 0c-65.7 0-120.1 48.7-129 112zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3z"/></svg>
                        <p class="text-[13px] text-gray-500   hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 " href="#">
                            Submitted by {{ $user->name }}
                        </p>
                    </li>

                    <li class="flex items-center gap-x-2.5">
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        <a class="text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 " 
                        href="mailto:{{ $user->email }}">
                            {{ $user->email }}
                        </a>
                    </li>
                
                    
                </ul> 
                <div class="text-end">


                    @if($project->status !== "approved")

                        @if(
                            Auth::user()->hasRole('DSI God Admin') || Auth::user()->hasRole('Admin') || 
                            (Auth::user()->hasRole('User') && $project->created_by == Auth::id() )
                        )

                            <a href="{{ route('project.index') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                Cancel
                            </a>

                            
                            <button {{ $project->allow_project_submission ? '' : 'disabled' }} type="button"
                                wire:click="submit_project({{ $project_id }})"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                Submit
                            </button> 

                            @if($project->allow_project_submission == true)
                                <a href="{{ route('project.edit',['project' => $project]) }}" 
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-sky-600 text-white hover:bg-sky-700 focus:outline-none focus:bg-sky-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Edit
                                </a> 
                            @endif
                            

                        @endif

                    @else
                        <p class="text-sm text-lime-600  ">
                            Project is approved
                        </p>
                    @endif
                    
                </div>
                
                    

            </div>
            <!-- End About -->
                    
        </div> 
    </div>
    <!-- End Card -->


    <aside class="col-span-12  mt-2  ">
        <div class="bg-white rounded-xl shadow  ">
            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 ">
                        Project Subscribers  
                    </h2>
                    <p class="text-gray-500 text-xs">Users that will be notified on project updates</p>
                </div> 

                {{-- <label for="name" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                    Search for User Name
                </label>

                <input type="text" wire:model.live="query" placeholder="Type to search..." class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "> --}}
                
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
                                    {{-- <button wire:click="removeSubscriber({{ $index }})" class="text-red-500">❌</button> --}}
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

        const location_directions = @json($location_directions);
        const latitude = parseFloat(location_directions[0][0].latitude) ? parseFloat(location_directions[0][0].latitude) : 13.4443;
        const longitude = parseFloat(location_directions[0][0].longitude) ? parseFloat(location_directions[0][0].longitude) : 144.7937;

        console.log("Latitude:", latitude, "Longitude:", longitude);

        console.log(latitude);
        function initMap() {

            


            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: latitude, lng: longitude }, // Centered on Guam
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

            searchBox.addListener("places_changed", function () {
                let places = searchBox.getPlaces();
                if (places.length == 0) return;

                let place = places[0];
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);


                @this.set('latitude', place.geometry.location.lat());
                @this.set('longitude', place.geometry.location.lng());
                @this.set('location', place.name);

                    

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
