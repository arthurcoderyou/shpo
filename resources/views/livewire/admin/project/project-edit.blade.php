<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto grid grid-cols-12 gap-x-2">
     
    {{-- <div wire:loading class="loading-overlay"> --}}
        <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    {{-- </div> --}}
    

    <form class=" col-span-12 sm:col-span-10" wire:submit="save">
        <!-- Card -->
        <div class="bg-white rounded-xl shadow dark:bg-neutral-900">


            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                    Edit project
                    </h2>
                </div>
                <!-- End Col -->


                <div class="grid grid-cols-12 gap-x-2  ">

                    <div class="space-y-2 col-span-12 my-1  ">
                        <h2>Search and Save Location</h2>
                        {{-- <input type="text" id="search-box"  wire:model="location"> --}}
                        {{-- <input type="text" id="search-box" placeholder="Search location" wire:model="location"> --}}
                        
      

                        <input
                        autofocus autocomplete="location"
                        wire:model="location"
                          placeholder="Search location"
                        id="search-box" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">


    
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

                    <div class="space-y-2 col-span-12 sm:col-span-4 ">
                        <label for="name" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Name
                        </label>

                        <input
                        autofocus autocomplete="name"
                        wire:model="name"
                        id="name" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">

                        @error('name')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-4 ">
                        <label for="federal_agency" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Federal Agency
                        </label>

                        <input
                        autofocus autocomplete="federal_agency"
                        wire:model="federal_agency"
                        id="federal_agency" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">

                        @error('federal_agency')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-4  ">
                        <label for="type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Type
                        </label>


                        <select
                        autocomplete="type"
                        wire:model="type"
                        id="type"
                        class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
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
                        id="type" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder=""> --}}

                        @error('type')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>




                    <div class="space-y-2 col-span-12   ">
                        <label for="description" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Description
                        </label>

                        <textarea
                        autofocus autocomplete="description"
                        wire:model="description"
                        id="description"  class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder=""></textarea>

                        @error('description')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12     ">
                        

                        

                        @php
                            function isImageMime($filename) {
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                return in_array($extension, $imageExtensions);
                            }
                        @endphp
                        

                        @if(isset($existingFiles) && count($existingFiles) > 0)
                        <div class="border border-blue-200 rounded-lg p-2 my-2.5">


                            <label for="description" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                Attachments
                            </label>

                            @php($index = 1)

                            @foreach($existingFiles as $date => $project_documents)
                                

                                <div class="hs-accordion-group"  >

                                    <div class="hs-accordion  " id="attachment-{{ $index }}">
                                        <button type="button" class="hs-accordion-toggle   
                                        @if( $index == 1) active @endif hs-accordion-active:text-blue-600 py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none dark:hs-accordion-active:text-blue-500 dark:text-neutral-200 dark:hover:text-neutral-400 dark:focus:text-neutral-400" aria-expanded="false" aria-controls="hs-basic-collapse-{{ $index }}">

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
                                                            <label class="inline-block text-sm font-medium text-gray-800 dark:text-neutral-200 mb-1">
                                                                {{ $document->document_type->name }}
                                                            </label> 

                                                            @foreach ($document->project_attachments as $attachment)
                                                                
                                                                <div class="dz-flex dz-items-center dz-justify-between dz-gap-2 dz-border dz-rounded dz-border-gray-200 dz-w-full dz-h-auto dz-overflow-hidden dark:dz-border-gray-700">
                                                                    <div class="dz-flex dz-items-center dz-gap-3">

                                                                        <?php 
                                                                            $attachment_file = asset('storage/uploads/project_attachments/' . $attachment->attachment);
                                                                        ?>


                                                                        @if(isImageMime($attachment_file))
                                                                            <div class="dz-flex-none dz-w-14 dz-h-14">
                                                                                <img src="{{ $attachment_file  }}" class="dz-object-fill dz-w-full dz-h-full" alt="{{ $attachment_file  }}">
                                                                            </div>
                                                                        @else
                                                                            <div class="dz-flex dz-justify-center dz-items-center dz-w-14 dz-h-14 dz-bg-gray-100 dark:dz-bg-gray-700">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="dz-w-8 dz-h-8 dz-text-gray-500">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                                                </svg>
                                                                            </div>
                                                                        @endif
                                                                        <div class="dz-flex dz-flex-col dz-items-start dz-gap-1">
                                                                            <div class="dz-text-center dz-text-slate-900 dz-text-sm dz-font-medium dark:dz-text-slate-100">{{ $attachment->attachment }}</div>
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
                                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="dz-w-6 dz-h-6 dz-text-black dark:dz-text-white">
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

                        <label for="description" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Add New Attachments
                        </label>


                         <!-- Dynamic Project Documents Section -->
                        <div class="space-y-2 col-span-12  mt-5">
                            @foreach($projectDocuments as $index => $document)
                                <div class="border border-black p-2 rounded-md mb-4">
                                    <label class="inline-block text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        Submission Type
                                    </label>
                                    <select
                                        class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                        wire:model.live="projectDocuments.{{ $index }}.document_type_id"
                                        
                                        >
                                        <option value="">Select Document Type</option>
                                        @foreach($documentTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach

                                        


                                    </select>

                                    @error("projectDocuments.".$index.".document_type_id")
                                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                    @enderror

                                    <label class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                        Document Attachments ( Only PNG, JPEG, JPG, PDF, DOCX, XLSX, CSV, TXT, and ZIP files are allowed. )
                                    </label>
                                    <input class="block w-full border border-gray-200 shadow-sm rounded-lg text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 file:bg-gray-50 file:border-0
                                    file:me-4
                                    file:py-3 file:px-4
                                    dark:file:bg-neutral-700 dark:file:text-neutral-400"
                                    type="file" wire:model.live="projectDocuments.{{ $index }}.attachments" multiple>

                                    @error("projectDocuments.".$index.".attachments")
                                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                    @enderror

                                    

                                    <!-- Show Selected Files Before Upload -->
                                    <div class="my-2">
                                        @if(isset($projectDocuments[$index]['attachments']) && count($projectDocuments[$index]['attachments']) > 0) 
                                            <p class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                                                Selected Files: (Choose files again to change the uploaded list)
                                            </p>
                                            @foreach($projectDocuments[$index]['attachments'] as $file)
                                                <div class="w-full mb-2 ">
                                                    <span class="block py-3 px-4 text-sm font-medium bg-white border border-gray-200 text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg dark:bg-neutral-900 dark:border-neutral-700 dark:text-white">{{ $file->getClientOriginalName() }}</span>
                                                </div>
                                            @endforeach



                                        @endif
                                    </div>

                                    {{-- <!-- Remove Button -->
                                    <button type="button" wire:click="removeProjectDocument({{ $index }})" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                        Remove
                                    </button> --}}
                                    
                                </div>
                            @endforeach
                            {{-- <div class="mb-2">
                                <button class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none" type="button" wire:click="addProjectDocument">+ Add Document</button>
                            </div> --}}

                        </div> 


                    </div>

                    


                </div>
                <!-- End Grid -->
 
                <!-- section visible to admin only -->
                {{-- @if( Auth::user()->hasRole('DSI God Admin') && Auth::user()->hasPermissionTo('timer edit') )  --}}
                <!-- Grid -->
                <div class="grid grid-cols-12 gap-x-2 
                {{ ( Auth::user()->hasRole('DSI God Admin')  || Auth::user()->hasRole('Reviewer') 

                )  ? 'block' : 'hidden'
                
                
                }}
                ">

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="submitter_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Submitter duration
                        </label>

                        <input
                        min="1"
                        autofocus autocomplete="submitter_response_duration"
                        wire:model.live="submitter_response_duration"
                        
                        id="submitter_response_duration" type="number" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">

                        @error('submitter_response_duration')
                            <p class="text-sm text-red-600 mt-2">{{ $message  }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="submitter_response_duration_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Submitter duration type
                        </label>

                        <select 
                        autofocus autocomplete="submitter_response_duration_type"
                        wire:model.live="submitter_response_duration_type"
                        id="submitter_response_duration_type" 
                        class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
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
                        <label for="submitter_due_date" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Submitter response due date
                        </label>

                        <input readonly 
                        {{-- autofocus autocomplete="submitter_due_date"
                        wire:model.live="submitter_due_date" --}}
                        value="{{ \Carbon\Carbon::parse($submitter_due_date)->format('d M, h:i A') }}"
                        id="submitter_due_date" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">

                        @error('submitter_due_date')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>
                    
                    
                </div>

                <div class="grid grid-cols-12 gap-x-2 
                {{ ( Auth::user()->hasRole('DSI God Admin')  || Auth::user()->hasRole('Reviewer') 

                )  ? 'block' : 'hidden'
                
                
                }}
                ">

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="reviewer_response_duration" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Reviewer duration
                        </label>

                        <input
                        min="1"
                        autofocus autocomplete="reviewer_response_duration"
                        wire:model.live="reviewer_response_duration"
                        id="reviewer_response_duration" type="number" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">

                        @error('reviewer_response_duration')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12 sm:col-span-3  ">
                        <label for="reviewer_response_duration_type" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Reviewer duration type
                        </label>

                        <select 
                        autofocus autocomplete="reviewer_response_duration_type"
                        wire:model.live="reviewer_response_duration_type"
                        id="reviewer_response_duration_type" 
                        class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
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
                        <label for="reviewer_due_date" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Reviewer response due date
                        </label>

                        <input readonly 
                        {{-- autocomplete="reviewer_due_date"
                        wire:model.live="reviewer_due_date" --}}    
                        value="{{ \Carbon\Carbon::parse($reviewer_due_date)->format('d M, h:i A') }}"
                        id="reviewer_due_date" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="">

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
                <!-- End Grid -->
                {{-- @endif --}}


                @if(Auth::user()->hasRole('User')) 
                <!-- End Grid -->
                <p class="text-sm text-gray-600 mt-2">{{ !empty($reviewer_due_date) ? 'Expect to get a review at '.\Carbon\Carbon::parse($reviewer_due_date)->format('d M, h:i A') : '' }}</p>
                @endif

                <div class="mt-5 flex justify-center gap-x-2">
                    <a href="{{ route('project.index') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                        Cancel
                    </a>
                    <button type="submit" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                    Save
                    </button>
                    
                    @if( Auth::user()->hasRole('DSI God Admin') || Auth::user()->can('project submit') )
                    <button {{ $project->allow_project_submission ? '' : 'disabled' }} type="button"
                        onclick="confirm('Are you sure, you want to submit this project?') || event.stopImmediatePropagation()"
                        wire:click.prevent="submit_project({{ $project_id }})"
                        
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
        <div class="bg-white rounded-xl shadow dark:bg-neutral-900 ">
            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                        Project Subscribers  
                    </h2>
                    <p class="text-gray-500 text-xs">Users that will be notified on project updates</p>
                </div> 

                <label for="name" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                    Search for User Name
                </label>

                <input type="text" wire:model.live="query" placeholder="Type to search..." class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                
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
