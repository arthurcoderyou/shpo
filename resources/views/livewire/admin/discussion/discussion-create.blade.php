<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto"
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
    saerch_project() {
        this.showModal = false;
        {{-- $wire.search = '';  --}}
    }
}"


>

    <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div>

    <form wire:submit="save">
        <!-- Card -->
        <div class="bg-white rounded-xl shadow dark:bg-neutral-900">


            <div class="  p-4">

                <div class="sm:col-span-12">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                    Create New Discussion
                    </h2>
                </div>
                <!-- End Col -->

                <!-- Grid -->
                <div class="grid grid-cols-12 gap-x-2  ">


                    <div class="space-y-2 col-span-12   ">
                        <label for="title" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Title
                        </label>

                        <input
                        placeholder="Add a title"
                        wire:model="title"
                        id="title" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"  >

                        @error('title')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12  ">
                        <label for="description" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Description
                        </label>

                        <input
                        placeholder="What's on your mind?"
                        wire:model="description"
                        id="description" type="text" class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"  >


                        

                        @error('description')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>



                    <div class="space-y-2 col-span-12    ">
                        <label for="forum_id" class="inline-block text-sm font-medium text-gray-800 mt-2.5 dark:text-neutral-200">
                            Forum
                        </label>

                        <input 
                        wire:model="forum_id"
                        type="hidden"
                        id="forum_id"  >

                        <button type="button"
                        @click="showModal = true" 
                        @keydown.window="handleKeydown"  
                        class="py-2 px-3 pe-11 block w-full border-2 text-start border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                            {{ $selected_forum ? $selected_forum->title : 'Select Forum' }}    
                        </button>


                        @error('forum_id')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>



                     




                </div>
                <!-- End Grid -->
 

                <div class="mt-5 flex justify-center gap-x-2">
                    <a href="{{ route('forum.index') }}" class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
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



    


    <!-- Forum modal-->
    @teleport('body')
        <div x-show="showModal" x-trap="showModal" class="relative z-10 " aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <!-- <div class="fixed inset-0 z-10 w-screen overflow-y-auto py-10"> -->
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto py-10">
                <div class="flex justify-center p-4 sm:p-0">
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div @click.outside="showModal = false" class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="w-full px-1 pt-1" x-data="{
                                searchPosts(event) {
                                    document.getElementById('searchInput').focus();
                                    event.preventDefault();
                                }
                            }">
                                <form action="" autocomplete="off">
                                    <input
                                    autocomplete="off"
                                    wire:model.live.throttle.500ms="forum_search" type="text" id="searchInput"
                                    name="searchInput"
                                    class="block w-full flex-1 py-2 px-3 mt-2 outline-none border-none rounded-md bg-slate-100"
                                    placeholder="Search for project name ..." @keydown.slash.window="searchPosts" />
                                </form>
                                <div class="mt-2 w-full overflow-hidden rounded-md bg-white">

                                    
                                        @if(!empty($results) && count($results) > 0)
                                            <div class=" py-2 px-3 border-b border-slate-200 text-sm font-medium text-slate-500">

                                                All Projects <strong>(Click to select a project)</strong>

                                            </div>

                                            @foreach ($results as $result)
                                                <div class="cursor-pointer py-2 px-3 hover:bg-slate-100 bg-white border border-gray-200 shadow-sm rounded-xl mb-1"
                                                wire:click="select_forum('{{  $result->id }}')"
                                                @click="showModal = false"
                                                >
                                                    <p class="text-sm font-medium text-gray-600 cursor-pointer flex items-center gap-3">
                                                        

                                                        <div class="max-w-full text-wrap ">
                                                            <div class="px-2 py-2   text-wrap">
                                                                

                                                                <span class="text-sm text-gray-600 dark:text-neutral-400">
                                                                    <strong>{{ $result->title }}</strong> 
                                                                    {{-- <hr>
                                                                    {{ $result->description }}  --}}
                                                                </span> 

                                                                    

                                                            </div>
                                                        </div>

                                                        

                                                        <div class="max-w-full size-auto whitespace-nowrap  ">
                                                            <div class="px-2 py-2   max-h-52 text-wrap overflow-auto">
                                                                <span class="text-sm text-gray-600 dark:text-neutral-400 ">
                                                                    {{ $result->description ? $result->description : '' }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                    </p>
                                                </div>
                                            @endforeach

                                        @else
                                            <div class=" py-2 px-3 border-b border-slate-200 text-sm font-medium text-slate-500">

                                                <div class="mb-2 bg-red-50 border-s-4 border-red-500 p-4 dark:bg-red-800/30" role="alert" tabindex="-1" aria-labelledby="hs-bordered-red-style-label">
                                                    <div class="flex">
                                                        <div class="shrink-0">
                                                            <!-- Icon -->
                                                            <span class="inline-flex justify-center items-center size-8 rounded-full border-4 border-red-100 bg-red-200 text-red-800 dark:border-red-900 dark:bg-red-800 dark:text-red-400">
                                                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M18 6 6 18"></path>
                                                                    <path d="m6 6 12 12"></path>
                                                                </svg>
                                                            </span>
                                                            <!-- End Icon -->
                                                        </div>
                                                        <div class="ms-3">
                                                            <h3 id="hs-bordered-red-style-label" class="text-gray-800 font-semibold dark:text-white">
                                                            Project not found
                                                            </h3>
                                                            <p class="text-sm text-gray-700 dark:text-neutral-400">

                                                            Search for name, description, agency or related data
                                                            </p>
                                                        </div>



                                                    </div>
                                                </div>



                                            </div>
                                        @endif



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endteleport
    <!-- ./ Forum modal-->
</div>
<!-- End Card Section -->
