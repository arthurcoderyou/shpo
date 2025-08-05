<!-- Table Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    <div wire:loading style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
        <div></div>
        <div></div>
    </div>

    <!-- Card -->
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden ">
            <!-- Header -->
            <div class="w-full px-3 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 ">
                <div>
                     
                    <h2 class="text-xl font-semibold text-gray-800  inline-flex items-center gap-x-2">
                        Notifications 
                        <span title="Unread notifications"
                         class="shrink-0 flex justify-center items-center size-[30px] text-sm bg-lime-500 text-white rounded-full  ">{{ $unread_count ?? 0 }}</span>
    
                    </h2>
                    <p class="text-sm text-gray-600 ">
                        Listing of notifications
                    </p>
                </div>

                <div>
                    <div class="inline-flex gap-x-2">


                        <input type="text" wire:model.live="search"
                            class="py-2 px-3 inline-flex items-center gap-x-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                            placeholder="Search">

                        <div class="inline-flex items-center gap-x-2   text-nowrap min-w-28">

                            <select wire:model.live="read_filter" class=" text-nowrap  py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                                <option value="all">Read Filter</option>
                                <option value="unread">Unread</option>
                                <option value="read">Read</option>
                            </select>
                        </div>

                            
                        <div class="inline-flex items-center gap-x-2  text-nowrap min-w-28">

                            <select wire:model.live="date_filter" class=" text-nowrap py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                                <option value="">Filter date</option>
                                <option value="today">Today</option>
                                <option value="this_week">This week</option>
                                <option value="this_month">This month</option>
                            </select>
                        </div>

                        <div class="inline-flex items-center gap-x-2  text-nowrap min-w-28">

                            <select wire:model.live="sort_by" class=" text-nowrap  py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                                <option value="">Sort By</option>
                                <option value="latest">Latest</option>
                                <option value="oldest">Oldest</option> 
                            </select>
                        </div> 


                        <button
                                onclick="confirm('Are you sure, you want to mark this records as seen?') || event.stopImmediatePropagation()"
                                wire:click.prevent="markAsReadSelected"
                                {{ $count == 0 ? 'disabled' : '' }}
                                class="py-2 px-3 inline-flex items-center text-nowrap gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-lime-500 text-white shadow-sm hover:bg-lime-50 hover:text-lime-600 hover:border-lime-500 focus:outline-lime-500 focus:text-lime-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                                
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48L48 64zM0 176L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-208L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
                                Mark as read ({{ $count }})
                            </button>


                        {{-- @if( Auth::user()->can('activity log delete') || Auth::user()->can('system access global admin')) 
                            <button
                                onclick="confirm('Are you sure, you want to delete this records?') || event.stopImmediatePropagation()"
                                wire:click.prevent="deleteSelected"
                                {{ $count == 0 ? 'disabled' : '' }}
                                class="py-2 px-3  text-nowrap  inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-red-500 text-white shadow-sm hover:bg-red-50 hover:text-red-600 hover:border-red-500 focus:outline-red-500 focus:text-red-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                Delete ({{ $count }})
                            </button>
                        {{-- @endif --}}

                        
                    </div>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <table class="min-w-full divide-y divide-gray-200 ">
                <thead class="bg-gray-50 ">
                <tr>
                    <th scope="col" class="w-[5%] px-2 py-3 text-start">
                        <label for="hs-at-with-checkboxes-main" class="flex">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                wire:click="toggleSelectAll"
                                wire:change="updateSelectedCount"
                                class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                                id="hs-at-with-checkboxes-main">
                            <span class="sr-only">Checkbox</span>
                        </label>
                    </th>
 
                    <th scope="col" class="w-[85%] px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Message
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="w-[5%] px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Date
                            </span>
                        </div>
                    </th>

                    

                    <th scope="col" class="w-[5%] px-6 py-3 text-end"></th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 ">

                    @if(!empty($notifications) && count($notifications) > 0)
                        @foreach ($notifications as $index => $notification)
                            <tr>
                                <td class="w-4 whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <label for="notification_{{ $notification->id }}" class="flex">
                                            <input type="checkbox"
                                            wire:model="selected_records"
                                            wire:change="updateSelectedCount"
                                            class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                                            id="notification_{{ $notification->id }}"
                                            value="{{ $notification->id }}"
                                            >
                                            <span class="sr-only">Checkbox</span>
                                        </label>
                                    </div>
                                </td>

  
 

                                <td class="size-auto text-wrap  ">
                                    <div class="px-2 py-2">
                                        <span class="block text-sm text-gray-500  max-h-40 overflow-y-auto">
                                            {{ $notification->data['message'] ?? 'No message' }}
                                        </span>
                                           
                                    </div>
                                </td>



                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">
                                            <span class="block text-sm text-gray-500 ">
                                                {{ $notification->created_at->format('F j, Y g:i A') }}
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </td>
 


                                <td class="w-4 whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <!-- edit -->
                                        @if(!empty($notification->data['url']))
                                            {{-- <a href="{{ url($notification->data['url']) }}" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                                Open
                                            </a> --}}


                                            @php 
                                                

                                                //if unread
                                                if(empty($notification->read_at)){ // not read yet
                                                    $color = " bg-lime-500 text-white hover:bg-lime-500 focus:outline-none focus:bg-lime-500";
                                                    $title = "Open notification";
                                                }else{
                                                    $color = " bg-lime-800 text-white hover:bg-lime-800 focus:outline-none focus:bg-lime-800";
                                                    $title = "Already seen notification";
                                                }
                                                
                                            @endphp 


                                            <button
                                            {{-- onclick="confirm('Mark as read') || event.stopImmediatePropagation()" --}}
                                            {{-- wire:click="markAsReadandOpen('{{ $notification->id }}')"  --}}
                                            onclick="markAsReadAndOpen('{{ $notification->id }}')"
                                            title="{{ $title }}"
                                            class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent  {{  $color }}  disabled:opacity-50  ">
                                                @if(empty($notification->read_at)) <!--- not read yet -->
                                                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M255.4 48.2c.2-.1 .4-.2 .6-.2s.4 .1 .6 .2L460.6 194c2.1 1.5 3.4 3.9 3.4 6.5l0 13.6L291.5 355.7c-20.7 17-50.4 17-71.1 0L48 214.1l0-13.6c0-2.6 1.2-5 3.4-6.5L255.4 48.2zM48 276.2L190 392.8c38.4 31.5 93.7 31.5 132 0L464 276.2 464 456c0 4.4-3.6 8-8 8L56 464c-4.4 0-8-3.6-8-8l0-179.8zM256 0c-10.2 0-20.2 3.2-28.5 9.1L23.5 154.9C8.7 165.4 0 182.4 0 200.5L0 456c0 30.9 25.1 56 56 56l400 0c30.9 0 56-25.1 56-56l0-255.5c0-18.1-8.7-35.1-23.4-45.6L284.5 9.1C276.2 3.2 266.2 0 256 0z"/></svg>   
                                                     
                                                @else <!-- not read -->
                                                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48L48 64zM0 176L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-208L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
                                                @endif

                                            </button>



                                            
 

                                        @endif
 

                                        <!-- delete -->
                                        {{-- @if( Auth::user()->can('user delete')  ||  Auth::user()->can('system access global admin')) --}}
                                        <button
                                        onclick="confirm('Are you sure, you want to delete this record?') || event.stopImmediatePropagation()"
                                        wire:click.prevent="delete('{{ $notification->id }}')"
                                        type="button" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.7 23.7 0 0 0 -21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0 -16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/></svg>
                                        </button>
                                        {{-- @endif --}}



                                    </div>
                                </td>



                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th colspan="4" scope="col" class="px-6 py-3 text-start">
                                <div class="flex items-center gap-x-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                                    No records found
                                    </span>
                                </div>
                            </th>
                        </tr>
                    @endif
                </tbody>
            </table>
            <!-- End Table -->

            <!-- Footer -->
            <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 ">
                {{ $notifications->links() }}

                <div class="inline-flex items-center gap-x-2">
                    <p class="text-sm text-gray-600 ">
                    Showing:
                    </p>
                    <div class="max-w-sm space-y-3">
                    <select wire:model.live="record_count" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                        <option>200</option>
                    </select>
                    </div>
                    <p class="text-sm text-gray-600 ">
                        {{ count($notifications) > 0 ? 'of '.$notifications->total()  : '' }}
                    </p>
                </div>


            </div>
            <!-- End Footer -->


            </div>
        </div>
        </div>
    </div>
    <!-- End Card -->

    <script>
        function markAsReadAndOpen(notificationId) {
            fetch(`/notifications/read-and-open/${notificationId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Livewire.dispatch('notificationsUpdated');
                        window.open(data.url, '_blank');
                    } else {
                        alert(data.message || 'Something went wrong.');
                    }
                })
                .catch(() => {
                    alert('Failed to open notification.');
                });
        }
    </script>

</div>
<!-- End Table Section -->
