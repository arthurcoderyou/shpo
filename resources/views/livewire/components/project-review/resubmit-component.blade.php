<div>
     
    <!-- Card Section -->
    <div class=" grid grid-cols-12 gap-3 text-start">
        
         

        <div class=" col-span-12  "  >
            <!-- Card -->
            <div class="bg-white rounded-2xl border border-slate-200 ">
                <div class="p-4">

                    <!-- Submission information -->
                    <div  >
                        <legend class="text-sm font-semibold text-slate-900">{{ $project->name ?? "Project Unnamed" }}</legend>
                        <p class="mt-1 text-xs text-slate-500">{{ $project_document->document_type->name ?? "Document Unnamed" }}</p>
                    </div>


    
                    <!-- About -->
                    <div class=" ">
                            
                        @php
                            function isImageMime($filename) {
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                return in_array($extension, $imageExtensions);
                            }
                        @endphp
                        

                        @if(isset($existingFiles) && count($existingFiles) > 0)
                            @php
                                $index = 1;

                                $revision = count($existingFiles) ?? 1;
                            @endphp

                            @foreach($existingFiles as $date => $project_documents)

                                

                                <div class="hs-accordion-group">
                                    <div class="hs-accordion" id="attachment-{{ $index }}">
                                        <button type="button" class="hs-accordion-toggle 
                                            @if( $index == 1) active @endif
                                            hs-accordion-active:text-blue-600 py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none"
                                            aria-expanded="false" aria-controls="hs-basic-collapse-{{ $index }}">
                            
                                            <svg class="hs-accordion-active:hidden block  size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"></path>
                                                <path d="M12 5v14"></path>
                                            </svg>

                                            <svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"></path>
                                            </svg>
                                            Revision {{ $revision - 1 }}: <span class="text-slate-500 font-monospace">{{ $date }}</span>
                                        </button>
                            
                                        <div id="hs-basic-collapse-{{ $index }}" class="hs-accordion-content 
                                            @if($index !== 1) hidden @endif
                                            w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="attachment-{{ $index }}">
                                            
                                            <div class="dz-flex dz-flex-wrap dz-gap-x-10 dz-gap-y-2 dz-justify-start dz-w-full">
                                                @foreach ($project_documents as $attachment)
                                                    @php
                                                        $downloadUrl = route('ftp.download', ['id' => $attachment['id']]);
                                                        // Use the real filename (has extension) for mime check
                                                        $filename = $attachment['attachment'];

                                                        $local_url = asset('storage/'.$attachment['path'].'/'.$attachment['stored_name']);

                                                    @endphp
                                                
                                                    <div class="dz-flex dz-items-center dz-justify-between dz-gap-2 dz-border dz-rounded dz-border-gray-200 dz-w-full">
                                                        <div class="dz-flex dz-items-center dz-gap-3">
                                                            {{-- @if(isImageMime($filename))
                                                                <div class="dz-flex-none dz-w-14 dz-h-14">
                                                                    <img src="{{ $downloadUrl }}" class="dz-object-fill dz-w-full dz-h-full" alt="{{ $filename }}">
                                                                </div>
                                                            @else --}}
                                                                <div class="dz-flex dz-justify-center dz-items-center dz-w-14 dz-h-14 dz-bg-gray-100">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="dz-w-8 dz-h-8 dz-text-gray-500">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                                    </svg>
                                                                </div>
                                                            {{-- @endif --}}
                                                
                                                            <div class="dz-flex dz-flex-col dz-items-start dz-gap-1">
                                                                <div class="dz-text-center dz-text-slate-900 dz-text-sm dz-font-medium">{{ $filename }}</div>
                                                            </div>
                                                        </div>
                                                
                                                        <div class="dz-flex dz-items-center dz-mr-3">
                                                            {{-- <a href="{{ $downloadUrl }}"
                                                            download="{{ $filename }}"
                                                            class="px-3 py-2 text-xs font-medium text-center text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300">
                                                                
                                                                <x-svg.download  class="size-4 shrink-0" />
                                                            </a> --}}

                                                            <a href="{{ $local_url }}" 
                                                            
                                                            download="{{ $filename }}"
                                                            class="px-3 py-2 text-xs font-medium text-center text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300">
                                                                
                                                                <x-svg.download  class="size-4 shrink-0" />
                                                            </a>

                                                        </div>

                                                    

                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php($index++)
                                @php($revision--)
                            @endforeach
                    
                        @else
                            <div class="hs-accordion-group">
                                <div class="hs-accordion" id="attachment-index">

                                    <div  class=" 
                                        py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none"
                                        >
                                
                                        Attachments
                                    </div>



                                    <div class="flex items-center justify-center gap-2 border border-gray-200 rounded w-full h-14 bg-gray-100">
                                        <div class="text-center text-slate-900 text-sm font-medium">
                                            No Attachments Found. <a href="" class="text-sky-500 hover:text-blue-500">Click to add files</a>
                                        </div>
                                    </div>

                        
                                </div>
                            </div>

                        @endif


                        {{-- <ul class="mt-5 flex flex-col gap-y-3">
                                
                            
                            <li class="flex items-center gap-x-2.5">
                                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M304 128a80 80 0 1 0 -160 0 80 80 0 1 0 160 0zM96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM49.3 464l349.5 0c-8.9-63.3-63.3-112-129-112l-91.4 0c-65.7 0-120.1 48.7-129 112zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3z"/></svg>
                                <p class="text-[13px] text-gray-500   hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 " href="#">
                                    Submitted by {{ $project->updator->name }}
                                </p>
                            </li>

                            <li class="flex items-center gap-x-2.5">
                                <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                <a class="text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 " 
                                href="mailto:{{ $project->updator->email }}">
                                    {{ $project->updator->email }}
                                </a>
                            </li>
                        
                            
                        </ul>  --}}


                    </div>

                    {{-- <!-- End About -->
                    <form class="text-end" wire:submit="submit">

                      

                        <a href="{{ route('project.show',['project' => $project_document->id]) }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                            Cancel
                        </a>

                        
                        <button {{ $project_document->allow_project_submission ? '' : 'disabled' }} 
                            type="button"
                            onclick="confirm('Are you sure, you want to submit this project?') || event.stopImmediatePropagation()"
                            wire:click.prevent="submit_project_document({{ $project_document->id }})"
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                            Submit
                        </button> 
 
 
                        
                    </form> --}}
 
                </div> 
            </div>
            <!-- End Card -->



           
                <!-- New Project Document Attachments -->
                <form class="mt-4" wire:submit="save" 
                     
                    >
                    <!-- Project Details Section -->
                    <div class="bg-white rounded-2xl border border-slate-200  p-6 mb-6 space-y-6 ">
                        <div class="grid grid-cols-12 gap-x-2  ">

                        
                            <div class="space-y-2 col-span-12     ">
        
                            
                            
                                <label for="description" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                                    Add New Document Attachments
                                </label>
        
                                <livewire:dropzone
                                     
                                    wire:model="attachments"
                                    :rules="['file', 'mimes:png,jpeg,jpg,pdf,docx,xlsx,csv,txt,zip', 'max:20480']"
                                    :multiple="true" />
        

                                 
        
                                @error('files')
                                    <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                                @enderror 
        
                            </div>

                            {{-- <div class="col-span-12 space-y-2">
                                <label for="attachments" class="inline-block text-sm font-medium text-gray-800">
                                    Attachments (Optional)
                                </label>

                                <div class="relative">
                                    <input
                                        id="attachments"
                                        type="file"
                                        wire:model="attachments"
                                        multiple
                                        accept=".png,.jpeg,.jpg,.pdf,.docx,.xlsx,.csv,.txt,.zip"
                                        class="block w-full text-sm text-gray-600
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-md file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-gray-100 file:text-gray-700
                                            hover:file:bg-gray-200
                                            focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    />
                                </div>

                                <p class="text-xs text-gray-500">
                                    Allowed file types: PNG, JPG, PDF, DOCX, XLSX, CSV, TXT, ZIP (Max 20MB per file)
                                </p>

                                @error('attachments.*')
                                    <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div> --}}

                            <div class="mt-1.5 col-span-12 text-end ">
                                <a href="{{ route('project.project-document.show',[
                                        'project' => $project->id,
                                        'project_document' => $project_document->id,
                                    ]) }}" 
                                    wire:navigate
                                    
                                    class="py-2 px-3 inline-block items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Cancel
                                </a>

                                
                               <button {{ $project_document->allow_project_submission ? '' : 'disabled' }} 
                                    type="button"
                                    onclick="confirm('Are you sure, you want to submit this project?') || event.stopImmediatePropagation()"
                                    wire:click.prevent="save"
                                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                    Submit
                                </button> 
                            </div>
                        
        
                        </div>
                        <!-- End Grid -->
                    </div>

                </form>
                <!-- ./ New Project Document Attachments -->
          


        </div>
    

    </div>

</div>
