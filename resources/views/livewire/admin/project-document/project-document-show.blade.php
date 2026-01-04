 
<!-- Card Section -->
<div class=" p-4 sm:px-6 lg:px-8  mx-auto grid grid-cols-12 gap-x-2"
    x-data="{ selectedRecordId: null }"
>
     
    {{-- <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div> --}}

    <div class=" col-span-12  "  >
 
        <!-- Assets Overview -->
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6 pb-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs text-slate-500">All files</div>
                <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">{{ $counts['all'] }}</div><span class="text-[11px] text-slate-500">items</span></div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs text-slate-500">PDFs</div>
                <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">{{ $counts['pdf'] }}</div><span class="text-[11px] text-slate-500">files</span></div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs text-slate-500">Images</div>
                <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">{{ $counts['images'] }}</div><span class="text-[11px] text-slate-500">jpg/png</span></div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs text-slate-500">Videos</div>
                <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">{{ $counts['videos'] }}</div><span class="text-[11px] text-slate-500">mp4</span></div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs text-slate-500">Docs</div>
                <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">{{ $counts['docs'] }}</div><span class="text-[11px] text-slate-500">docx/xlsx</span></div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs text-slate-500">Other</div>
                <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">{{ $counts['other'] }}</div><span class="text-[11px] text-slate-500">misc</span></div>
            </div>
        </div>


        <!-- Project Documents -->
        <div class="bg-white rounded-2xl border border-slate-200 ">
            <div class="p-4"> 
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


                                                        @if(empty($attachment['last_submitted_at']))
                                                        <button 
                                                            type="button"
                                                            onclick="confirm('Are you sure, you want to delete this attachment?') || event.stopImmediatePropagation()" 
                                                            wire:click.prevent="delete({{ $attachment['id'] }})" 
                                                            class="dz-flex dz-justify-center dz-items-center dz-w-14 dz-h-14 dz-bg-red-100 hover:dz-bg-red-200 dz-rounded-lg dz-transition dz-duration-200"
                                                            title="Delete File"
                                                        >
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="dz-w-6 dz-h-6 dz-text-red-600">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>

                                                        @endif
                                                        {{-- - 
                                                        <a href="{{ $downloadUrl }}" 
                                                         
                                                        download="{{ $filename }}"
                                                        class="px-3 py-2 text-xs font-medium text-center text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300">
                                                            
                                                            <x-svg.download  class="size-4 shrink-0" />
                                                        </a>
                                                        --}}


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

                    @if(Auth::user()->can('system access global admin') || Auth::user()->can('project add attachment'))
                        <div class="py-4">

                            @if($project_document->allow_project_submission == true)
                                <!-- Add new project document -->
                                <a href="{{ route('project.project_document.edit_attachments',['project' => $project->id,'project_document' => $project_document->id]) }}" 
                                    {{-- target="_blank"  --}}
                                    wire:navigate
                                    class="py-1 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border  bg-blue-500 text-white hover:bg-blue-700 focus:outline-hidden focus:border-blue-400  disabled:opacity-50 disabled:pointer-events-none">
                                    Add/Edit attachments
                                </a>
                                <!-- Add new project document -->
                            @else
                                <!-- Add new project document -->
                                <button disabled class="py-1 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border disabled:opacity-50 bg-blue-500 text-white hover:bg-blue-700 focus:outline-hidden focus:border-blue-400  disabled:opacity-50 disabled:pointer-events-none">
                                    Add/Edit attachments
                                </button>
                                <!-- Add new project document -->
                            @endif

                        </div>
                    @endif
                

                </div>

                <!-- End About -->
                <form class="text-end" wire:submit="submit">

                    
                    @if($project_document->status !== "approved")

                        @if(
                            Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || 
                            (Auth::user()->can('system access user') && $project_document->created_by == Auth::id() )
                        )

                            <a href="{{ route('project.project-document.index',['project' => $project->id]) }}"
                                wire:navigate
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                Cancel
                            </a>

                            
                            <button {{ $project_document->allow_project_submission ? '' : 'disabled' }} type="button"
                                onclick="confirm('Are you sure, you want to submit this project document?') || event.stopImmediatePropagation()"
                                wire:click.prevent="submit_project_document({{ $project_document->id }})"
                                class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                Submit
                            </button> 

                            


                        @endif

                    @else
                        <p class="text-sm text-lime-600  ">
                        Project document is approved
                        </p>
                    @endif
                    
                </form>

                

                        
            </div> 
        </div>
        <!-- End Project Documents -->
    </div>
    




    @if($confirmingDelete)
        <form wire:submit.prevent="executeDelete" class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
                <h2 class="text-lg font-semibold text-red-600">Confirm Deletion</h2>
                <p class="text-sm text-gray-700 mt-2">
                    This action <strong>cannot be undone</strong>. Please enter your password to confirm.
                </p>

                <div class="mt-4" x-data="{ show: false }">
                    <label for="passwordConfirm" class="block text-sm font-medium text-gray-700">Your Password</label>
                    <div class="relative mt-1">
                        <input :type="show ? 'text' : 'password'" wire:model.defer="passwordConfirm" id="passwordConfirm"
                            class="block w-full rounded-md border-gray-300 pr-10 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <button type="button"
                                x-on:click="show = !show"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm text-gray-600 hover:text-gray-900 focus:outline-none"
                                tabindex="-1">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.045 10.045 0 014.724-5.735M6.182 6.182l11.636 11.636M17.818 17.818L6.182 6.182"/>
                            </svg>
                        </button>
                    </div>
                    @if($passwordError)
                        <p class="text-sm text-red-500 mt-1">{{ $passwordError }}</p>
                    @endif
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button wire:click="$set('confirmingDelete', false)"
                            type="button"
                            class="px-4 py-2 text-sm rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm rounded-md text-white bg-red-600 hover:bg-red-700">
                        Confirm Delete
                    </button>
                </div>
            </div>
        </form>
    @endif

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
                    window.pageProjectDocumentId = @json(optional(request()->route('project_document'))->id ?? request()->route('project_document') ?? null);
                    console.log(window.pageProjectDocumentId);

                    const pageProjectDocumentId = window.pageProjectDocumentId; // can be null
                    // 2) Conditionally listen to the model-scoped user channel
                    if (pageProjectDocumentId) {
                        console.log(`listening to : ${pageProjectDocumentId}`);
                        window.Echo.private(`project_document.${pageProjectDocumentId}`)
                            .listen('.event', (e) => {
                                console.log('[project_document model-scoped]');

                                let dispatchEvent = `projectDocumentEvent.${pageProjectDocumentId}`;
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
