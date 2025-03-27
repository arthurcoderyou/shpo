@if( $index == 1)
                                        <div class="hs-accordion  " id="attachment-{{ $index }}">
                                            <button type="button" class="hs-accordion-toggle   active hs-accordion-active:text-blue-600 py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none dark:hs-accordion-active:text-blue-500 dark:text-neutral-200 dark:hover:text-neutral-400 dark:focus:text-neutral-400" aria-expanded="false" aria-controls="hs-basic-collapse-{{ $index }}">

                                                <svg class="hs-accordion-active:hidden block  size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5 12h14"></path>
                                                    <path d="M12 5v14"></path>
                                                </svg>

                                                <svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5 12h14"></path>
                                                </svg>
                                                {{ $date }}
                                            </button>


                                            

                                            <div id="hs-basic-collapse-{{ $index }}" class="hs-accordion-content  w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="attachment-{{ $index }}">
                                            

                                                <div class="dz-flex dz-flex-wrap dz-gap-x-10 dz-gap-y-2 dz-justify-start dz-w-full  ">
                                                    @foreach($file->project_attachments as $attachment)
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



                                            </div>
                                        </div>
                                    @else
                                     
                                        <div class="hs-accordion" id="attachment-{{ $index }}">
                                            <button type="button" class="hs-accordion-toggle hs-accordion-active:text-blue-600 py-3 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 hover:text-gray-500 focus:outline-none focus:text-gray-500 rounded-lg disabled:opacity-50 disabled:pointer-events-none dark:hs-accordion-active:text-blue-500 dark:text-neutral-200 dark:hover:text-neutral-400 dark:focus:text-neutral-400" aria-expanded="false" aria-controls="hs-basic-collapse-{{ $index }}">
                                                <svg class="hs-accordion-active:hidden block size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5 12h14"></path>
                                                    <path d="M12 5v14"></path>
                                                </svg>
                                                <svg class="hs-accordion-active:block hidden size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5 12h14"></path>
                                                </svg>
                                                {{ $file->created_at->format('M d, Y h:i A') }}
                                            </button>
                                            <div id="hs-basic-collapse-{{ $index }}" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region" aria-labelledby="attachment-{{ $index }}">
                                                  
                                                <div class="dz-flex dz-flex-wrap dz-gap-x-10 dz-gap-y-2 dz-justify-start dz-w-full  ">
                                                    @foreach($file->project_attachments as $attachment)
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
                                                
                                                
                                                
                                            </div>
                                        </div>
                                    @endif