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
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden  ">
            <!-- Header -->
            <div class="px-3 py-2  grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 ">
                <div>
                 
                <h2 class="text-xl font-semibold text-gray-800  inline-flex items-center gap-x-2">
                    Activity Logs<span class="shrink-0 flex justify-center items-center size-[30px] text-sm bg-black text-white rounded-full  ">{{ $activity_logs->count() ?? 0 }}</span>

                </h2>
                {{-- <p class="text-sm text-gray-600 ">
                    Listing of actions by users
                </p> --}}
                </div>

                <div>
                <div class="inline-flex gap-x-2">


                    <input type="text" wire:model.live="search"
                        class="py-2 px-3 inline-flex items-center gap-x-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                        placeholder="Search">


                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="sort_by" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                            <option selected value="">Sort By</option>
                            <option>Log Activity A - Z</option>
                            <option>Log Activity Z - A</option>
                            <option>User A - Z</option>
                            <option>User Z - A</option>
                            <option>Latest Added</option>
                            <option>Oldest Added</option>
                            <option>Latest Updated</option>
                            <option>Oldest Updated</option>
                        </select>
                    </div>

                    @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->can('activity log delete'))
                        <button
                            onclick="confirm('Are you sure, you want to delete this records?') || event.stopImmediatePropagation()"
                            wire:click.prevent="deleteSelected"
                            {{ $count == 0 ? 'disabled' : '' }}
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-red-500 text-white shadow-sm hover:bg-red-50 hover:text-red-600 hover:border-red-500 focus:outline-red-500 focus:text-red-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none " >
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                            Delete ({{ $count }})
                        </button>
                    @endif


                </div>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <table class="min-w-full divide-y divide-gray-200 ">
                <thead class="bg-gray-50 ">
                <tr>
                    <th scope="col" class="px-2 py-3 text-start">
                        <label for="hs-at-with-checkboxes-main" class="flex">
                            <input
                                type="checkbox"
                                wire:model="selectAll"
                                wire:click="toggleSelectAll"
                                wire:change="updateSelectedCount"
                                class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                                id="hs-at-with-checkboxes-main">
                            <span class="sr-only">Checkbox</span>
                        </label>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Logged User
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Logged Activity
                            </span>
                        </div>
                    </th>



                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Logged at
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-end"></th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 ">

                    @if(!empty($activity_logs) && count($activity_logs) > 0)
                        @foreach ($activity_logs as $activity_log)
                            <tr>
                                <td class="w-4 whitespace-nowrap">
                                    <div class="px-2 py-2">
                                    <label for="activity_log_{{ $activity_log->id }}" class="flex">
                                        <input type="checkbox"
                                        wire:model="selected_records"
                                        wire:change="updateSelectedCount"
                                        class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                                        id="activity_log_{{ $activity_log->id }}"
                                        value="{{ $activity_log->id }}"
                                        >
                                        <span class="sr-only">Checkbox</span>
                                    </label>
                                    </div>
                                </td>


                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                            <div class="grow">
                                                <span class="block text-sm font-semibold text-gray-800 ">{{ $activity_log->user->name }}</span>
                                                <span class="block text-sm text-gray-500 ">{{ $activity_log->user->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="size-auto whitespace-nowrap hs-tooltip hs-tooltip-toggle">
                                    <div class="px-2 py-2">
                                        <span class="block text-sm text-gray-500 text-wrap max-w-xl">{{ $activity_log->log_action }}</span>
                                    </div>

                                    <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded-md shadow-2xs max-w-md text-wrap" role="tooltip">
                                        {{ $activity_log->log_action }}
                                    </span>

                                </td>
                                

                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <span class="text-sm text-gray-500 ">{{ \Carbon\Carbon::parse($activity_log->created_at)->format('M d Y, h:i A') }}</span>
                                    </div>
                                </td>
                                <td class="size-px whitespace-nowrap">
                                    <div class="px-6 py-1.5">
                                        @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->can('activity log delete'))
                                        <button
                                        onclick="confirm('Are you sure, you want to delete this record?') || event.stopImmediatePropagation()"
                                        wire:click.prevent="delete({{ $activity_log->id }})"
                                        type="button" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.7 23.7 0 0 0 -21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0 -16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/></svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <th scope="col" class="px-6 py-3 text-start">
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
                {{ $activity_logs->links() }}

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
                        {{ count($activity_logs) > 0 ? 'of '.$activity_logs->total()  : '' }}
                    </p>
                </div>


            </div>
            <!-- End Footer -->


            </div>
        </div>
        </div>
    </div>
    <!-- End Card -->
</div>
<!-- End Table Section -->

@section('script')
    <script type="module">
        // handler for activity logs
        window.Echo.private("activitylog")
            .listen('.created', (e) => {

                console.log('Received update event:', e.message);

                Livewire.dispatch('activitylogCreated');


            });
    </script>
@endsection