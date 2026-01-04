<!-- Card Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    {{-- <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div> --}}
    

    
    <!-- Card -->
    <div class="bg-white rounded-xl shadow ">


        <div class="  p-4">

            <div class="sm:col-span-12 flex justify-between align-self-center">
                <h2 class="text-lg font-semibold text-gray-800 ">
                    Project Timer
                </h2>

                {{-- @if(Auth::user()->can('system access global admin')  || Auth::user()->can('timer apply to all'))
                <button title="This is to apply the reviewer list here for all NOT APPROVED projects"
                    onclick="confirm('Are you sure, you want to apply this to all records?') || event.stopImmediatePropagation()"
                    wire:click.prevent="apply_to_all" 
                    class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-sky-500 text-white shadow-sm hover:bg-sky-50 hover:text-sky-600 hover:border-sky-500 focus:outline-sky-500 focus:text-sky-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M352 256c0 22.2-1.2 43.6-3.3 64l-185.3 0c-2.2-20.4-3.3-41.8-3.3-64s1.2-43.6 3.3-64l185.3 0c2.2 20.4 3.3 41.8 3.3 64zm28.8-64l123.1 0c5.3 20.5 8.1 41.9 8.1 64s-2.8 43.5-8.1 64l-123.1 0c2.1-20.6 3.2-42 3.2-64s-1.1-43.4-3.2-64zm112.6-32l-116.7 0c-10-63.9-29.8-117.4-55.3-151.6c78.3 20.7 142 77.5 171.9 151.6zm-149.1 0l-176.6 0c6.1-36.4 15.5-68.6 27-94.7c10.5-23.6 22.2-40.7 33.5-51.5C239.4 3.2 248.7 0 256 0s16.6 3.2 27.8 13.8c11.3 10.8 23 27.9 33.5 51.5c11.6 26 20.9 58.2 27 94.7zm-209 0L18.6 160C48.6 85.9 112.2 29.1 190.6 8.4C165.1 42.6 145.3 96.1 135.3 160zM8.1 192l123.1 0c-2.1 20.6-3.2 42-3.2 64s1.1 43.4 3.2 64L8.1 320C2.8 299.5 0 278.1 0 256s2.8-43.5 8.1-64zM194.7 446.6c-11.6-26-20.9-58.2-27-94.6l176.6 0c-6.1 36.4-15.5 68.6-27 94.6c-10.5 23.6-22.2 40.7-33.5 51.5C272.6 508.8 263.3 512 256 512s-16.6-3.2-27.8-13.8c-11.3-10.8-23-27.9-33.5-51.5zM135.3 352c10 63.9 29.8 117.4 55.3 151.6C112.2 482.9 48.6 426.1 18.6 352l116.7 0zm358.1 0c-30 74.1-93.6 130.9-171.9 151.6c25.5-34.2 45.2-87.7 55.3-151.6l116.7 0z"/></svg>
                    
                    APPLY TO ALL
                </button>
                @endif --}}

            </div>
            <!-- End Col -->
            <form wire:submit="save">
                <!-- Grid -->
                <div class="grid grid-cols-12 gap-x-2 mt-2  ">

                    <div class="space-y-2 col-span-12 sm:col-span-6  ">
                         
                        <x-ui.input    
                            id="submitter_response_duration"
                            name="submitter_response_duration"
                            type="number"
                            wire:model.live="submitter_response_duration"   
                            label="Submission duration (in days)"
                            required  
                            placeholder="Enter submission duration (in days)" 
                            :error="$errors->first('submitter_response_duration')"
 
 
                        />

                    </div>
 


                    <div class="space-y-2 col-span-12 sm:col-span-6  ">
                         
                        <x-ui.input    
                            id="reviewer_response_duration"
                            name="reviewer_response_duration"
                            type="number"
                            wire:model.live="reviewer_response_duration"   
                            label="Review duration (in days)"
                            required  
                            placeholder="Enter review duration (in days)" 
                            :error="$errors->first('reviewer_response_duration')"
 
 
                        />
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


                    


                    <div class="space-y-2 col-span-12 sm:col-span-4  ">
                        <label for="project_submission_open_time" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Project Submissions Open Time
                        </label>

                        <input 
                            id="project_submission_open_time"
                            autofocus autocomplete="project_submission_open_time"
                            wire:model.live="project_submission_open_time"
                            type="text"
                            placeholder="HH:MM AM/PM"
                        class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                        @error('project_submission_open_time')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>
 

                    <div class="space-y-2 col-span-12 sm:col-span-4  ">
                        <label for="project_submission_close_time" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Project Submissions Close Time
                        </label>

                        <input 
                            id="project_submission_close_time"
                            autofocus autocomplete="project_submission_close_time"
                            wire:model.live="project_submission_close_time"
                            type="text"
                            placeholder="HH:MM AM/PM"
                            class="py-2 px-3 pe-11 block w-full border-gray-200 shadow-sm rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " placeholder="">

                        @error('project_submission_close_time')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>



                    <div class="space-y-2 col-span-12 sm:col-span-4  ">
                        <label for="project_submission_restrict_by_time" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Enable time submission restriction 
                        </label>

                        <select 
                            autofocus autocomplete="project_submission_restrict_by_time"
                            wire:model.live="project_submission_restrict_by_time"
                            id="project_submission_restrict_by_time" 
                            class="py-2 px-3 pe-11  block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  ">
                            <option selected="">Select status</option>
                            <option value="true">Enable</option>
                            <option value="false">Disable</option> 
                        </select>

                        @error('project_submission_restrict_by_time')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>

                    <div class="space-y-2 col-span-12    ">
                        <label for="message_on_open_close_time" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Project Submissions Close Time
                        </label>

                        <textarea 
                        id="message_on_open_close_time"
                        autofocus autocomplete="message_on_open_close_time"
                        wire:model="message_on_open_close_time" name=""
                        class="py-2 px-3 sm:py-3 sm:px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none   " rows="3" placeholder="Message"></textarea>
                        @error('message_on_open_close_time')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror


                    </div>    
 


                    <div class="space-y-2 col-span-12">
                        <label for="message_on_open_close_time" class="inline-block text-sm font-medium text-gray-800 mt-2.5 ">
                            Project Submissions Active Days
                        </label>

                        <ul class="flex flex-col sm:flex-row">
        
                            <div class="grid sm:grid-cols-4 lg:grid-cols-8 gap-2">
                                <label for="select_all" class="flex p-3 w-full bg-white border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500  ">
                                    <span class="text-sm text-black ">Select All</span>
                                    <input wire:click="selectAll($event.target.checked)" type="checkbox" value="select_all" class="shrink-0 ms-auto mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "
                                    id="select_all" {{ $this->allDaysActive ? 'checked' : '' }}>
                                </label>
                            
                                @foreach ($DaysOfTheWeek as $day)
                                    <label for="{{ $day->day }}" class="flex  p-3 w-full bg-white border border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500  ">
                                        <span class="text-sm text-black ">{{ $day->day }}</span>
                                        <input wire:model.live="days.{{ $day->id }}.is_active" type="checkbox" value="1" class="shrink-0 ms-auto mt-0.5 border-gray-200 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "
                                        id="{{ $day->day }}" {{ $day->is_active ? 'checked' : '' }}>
                                    </label>
                                @endforeach
                            </div>
        
        
                        </ul>
        
                        @error('day')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
        
                    </div>
                    <!-- End Col -->
                    




                </div>
                <!-- End Grid -->

                

                <div class="mt-5 flex justify-center gap-x-2">




                    <a href="{{ route('project.index') }}" wire:navigate class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                        Cancel
                    </a>

                    @if(Auth::user()->can('system access global admin')  || Auth::user()->can('timer edit'))
                        <button
                            type="submit"
                            onclick="return confirm('⚠️ This action will update the system-wide project time settings.\n\nAll users, reviewers, and administrators will be notified via email.\n\nAre you sure you want to proceed?')"
                            class="py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none"
                        >
                            Update
                        </button>
                    @endif


                    {{-- <!-- Project subscribers button and box -->
                        <x-ui.project-timer.send-email-box /> 
                    <!-- ./ Project subscribers button and box --> --}}



                </div>

            </form>
        </div>
    </div>
    <!-- End Card --> 


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


</div>
<!-- End Card Section -->


@push('scripts')
    

    <script>
        function initProjectFlatpickr() {
            // Avoid double-init by checking if element exists
            const openInput  = document.querySelector('#project_submission_open_time');
            const closeInput = document.querySelector('#project_submission_close_time');

            if (openInput) {
                flatpickr(openInput, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "h:i K",
                    minuteIncrement: 30,
                    onChange: function (selectedDates, dateStr) {
                        // $wire.set('project_submission_open_time', dateStr);
                        @this.set('project_submission_open_time', dateStr);
                    },
                });
            }

            if (closeInput) {
                flatpickr(closeInput, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "h:i K",
                    minuteIncrement: 30,
                    onChange: function (selectedDates, dateStr) {
                        // $wire.set('project_submission_close_time', dateStr);
                        @this.set('project_submission_close_time', dateStr);
                    },
                });
            }
        }

        // First page load
        document.addEventListener('DOMContentLoaded', initProjectFlatpickr);

        // Livewire v3 wire:navigate navigation
        document.addEventListener('livewire:navigated', () => {
            initProjectFlatpickr();
        });
    </script>





@endpush

