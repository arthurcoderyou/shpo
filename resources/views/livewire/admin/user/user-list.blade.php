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
            <div class="px-3 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 ">
                <div>
                {{-- <h2 class="text-xl font-semibold text-gray-800 ">
                    Users
                </h2>
                <p class="text-sm text-gray-600 ">
                    Listing of users
                </p> --}}
                <h2 class="text-xl font-semibold text-gray-800  inline-flex items-center gap-x-2">
                    Users <span class="shrink-0 flex justify-center items-center size-[30px] text-sm bg-black text-white rounded-full  ">{{ $user_count ?? 0 }}</span>

                </h2>
                </div>

                <div>
                <div class="inline-flex gap-x-2 text-nowrap">


                    <input type="text" wire:model.live="search"
                        class=" py-2 px-3 inline-flex items-center gap-x-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none "
                        placeholder="Search">

                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="selected_role" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500   ">
                            <option value="">Sort Role</option>
                            <option value="no_role">No Role</option>
                            @if(!empty($roles))
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{  $role->name == "DSI God Admin" ? "Global Administrator" : $role->name  }}</option>
                                @endforeach
                            @endif
                            
                             
                        </select>
                    </div>

                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="role_request" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500   ">
                            <option value="">Sort Request</option>
                            <option value="user">User Request</option>
                            <option value="reviewer">Reviewer Request</option>
                             
                        </select>
                    </div>

                    <div class="inline-flex items-center gap-x-2">

                        <select wire:model.live="sort_by" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500   ">
                            <option value="">Sort By</option>
                            <option>Name A - Z</option>
                            <option>Name Z - A</option>
                            <option>Email A - Z</option>
                            <option>Email Z - A</option>
                            <option>Latest Added</option>
                            <option>Oldest Added</option>
                            <option>Latest Updated</option>
                            <option>Oldest Updated</option>
                        </select>
                    </div>

                    {{-- @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->can('user delete'))
                        <button
                            onclick="confirm('Are you sure, you want to delete this records?') || event.stopImmediatePropagation()"
                            wire:click.prevent="deleteSelected"
                            {{ $count == 0 ? 'disabled' : '' }}
                            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-red-500 text-white shadow-sm hover:bg-red-50 hover:text-red-600 hover:border-red-500 focus:outline-red-500 focus:text-red-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none   " >
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                            Delete ({{ $count }})
                        </button>
                    @endif --}}

                    @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->can('user create'))
                    <a href="{{ route('user.create') }}"
                        class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-yellow-500 text-white shadow-sm hover:bg-yellow-50 hover:text-yellow-600   hover:border-yellow-500 focus:outline-yellow-500 focus:text-yellow-500 focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none   " >
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm144 276c0 6.6-5.4 12-12 12h-92v92c0 6.6-5.4 12-12 12h-56c-6.6 0-12-5.4-12-12v-92h-92c-6.6 0-12-5.4-12-12v-56c0-6.6 5.4-12 12-12h92v-92c0-6.6 5.4-12 12-12h56c6.6 0 12 5.4 12 12v92h92c6.6 0 12 5.4 12 12v56z"/></svg>
                    </a>
                    @endif
                </div>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <table class="min-w-full divide-y divide-gray-200 ">
                <thead class="bg-gray-50 ">
                <tr>
                    {{-- <th scope="col" class="px-2 py-3 text-start">
                        <label for="hs-at-with-checkboxes-main" class="flex">
                            <input
                                type="checkbox"
                                wire:model.live="selectAll"
                                wire:click="toggleSelectAll"
                                wire:change="updateSelectedCount"
                                class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "
                                id="hs-at-with-checkboxes-main">
                            <span class="sr-only">Checkbox</span>
                        </label>
                    </th> --}}

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            User
                            </span>
                        </div>
                    </th>
                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Role
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Requested Role
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Verified
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Modified
                            </span>
                        </div>
                    </th>

                    <th scope="col" class="px-6 py-3 text-end"></th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 ">

                    @if(!empty($users) && count($users) > 0)
                        @foreach ($users as $user)
                            <tr>
                                {{-- <td class="w-4 whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <label for="user_{{ $user->id }}" class="flex">
                                            <input type="checkbox"
                                            wire:model="selected_records"
                                            wire:change="updateSelectedCount"
                                            class="shrink-0 border-gray-300 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  "
                                            id="user_{{ $user->id }}"
                                            value="{{ $user->id }}"
                                            >
                                            <span class="sr-only">Checkbox</span>
                                        </label>
                                    </div>
                                </td> --}}


                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">
                                            <span class="block text-sm font-semibold text-gray-800 ">{{ $user->name }}</span>
                                            <span class="block text-sm text-gray-500 ">{{ $user->email }}</span>
                                        </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">
                                            <span class="block text-sm text-gray-500 ">

                                                @if(!empty($user->roles->first()->name))
                                                    @if($user->roles->first()->name == "DSI God Admin")
                                                        Global Administrator
                                                    @else
                                                        {{ ucfirst($user->roles->first()->name) }}
                                                    @endif
                                                @endif
 
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </td>


                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow"> 
                                            <span class="block text-sm text-gray-500 ">{{ ucfirst( $user->role_request )}}</span>
                                        </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">
                                            <span class="block text-sm text-gray-500 ">
                                                {{ !empty($user->email_verified_at)  ? \Carbon\Carbon::parse($user->email_verified_at)->format('d M, H:i') : 'NOT VERIFIED' }}
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">
                                            <span class="block text-sm text-gray-500 ">
                                                {{ \Carbon\Carbon::parse($user->updated_at)->format('d M, H:i') }}
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </td>



                                <td class="w-4 whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->can('user edit'))
                                        <!-- edit -->
                                        <a href="{{ route('user.edit',['user' => $user->id]) }}" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M402.6 83.2l90.2 90.2c3.8 3.8 3.8 10 0 13.8L274.4 405.6l-92.8 10.3c-12.4 1.4-22.9-9.1-21.5-21.5l10.3-92.8L388.8 83.2c3.8-3.8 10-3.8 13.8 0zm162-22.9l-48.8-48.8c-15.2-15.2-39.9-15.2-55.2 0l-35.4 35.4c-3.8 3.8-3.8 10 0 13.8l90.2 90.2c3.8 3.8 10 3.8 13.8 0l35.4-35.4c15.2-15.3 15.2-40 0-55.2zM384 346.2V448H64V128h229.8c3.2 0 6.2-1.3 8.5-3.5l40-40c7.6-7.6 2.2-20.5-8.5-20.5H48C21.5 64 0 85.5 0 112v352c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V306.2c0-10.7-12.9-16-20.5-8.5l-40 40c-2.2 2.3-3.5 5.3-3.5 8.5z"/></svg>
                                        </a>
                                        @endif

                                        @if(Auth::user()->hasRole('DSI God Admin')  || Auth::user()->can('user delete'))
                                            <!-- delete -->
                                            {{-- @if( Auth::user()->can('user delete')  ||  Auth::user()->hasRole('DSI God Admin')) --}}
                                            <button
                                            onclick="confirm('Are you sure, you want to delete this record? This cannot be undone') || event.stopImmediatePropagation()"
                                            wire:click.prevent="delete({{ $user->id }})"
                                            type="button" class="py-2 px-3 inline-flex items-center gap-x-2  text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.7 23.7 0 0 0 -21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0 -16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/></svg>
                                            </button>
                                            {{-- @endif --}}
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
                {{ $users->links() }}

                <div class="inline-flex items-center gap-x-2">
                    <p class="text-sm text-gray-600 ">
                    Showing:
                    </p>
                    <div class="max-w-sm space-y-3">
                    <select wire:model.live="record_count" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500   ">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                        <option>200</option>
                    </select>
                    </div>
                    <p class="text-sm text-gray-600 ">
                        {{ count($users) > 0 ? 'of '.$users->total()  : '' }}
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
