<!-- Table Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

    <div wire:loading class="loading-overlay">
        <div style="color: #64d6e2" class="la-ball-clip-rotate-pulse la-3x preloader">
            <div></div>
            <div></div>
        </div>
    </div>

    <!-- Card -->
    <div class="flex flex-col">
        <form wire:submit="save" >
        <div class="-m-1.5 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden ">
            <!-- Header -->
            <div class="px-3 py-2 grid gap-3 md:flex md:justify-between md:items-center border-b border-gray-200 ">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 ">
                        Role: {{ $role->name == "DSI God Admin" ? "Global Administrator" : $role->name   }}
                    </h2>
                    <p class="text-sm text-gray-600 ">
                        Add Permissions to role
                    </p>
                </div>

                <div>
                    <div class="sm:col-span-2 flex gap-x-2 items-center justify-center">


                        <label for="select_all" class="flex py-2 px-3 w-32 bg-white border border-sky-500 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500   ">
                            <span class="text-sm text-black ">Select All</span>
                            <input wire:click="selectAll($event.target.checked)" type="checkbox" value="select_all" class="shrink-0 ms-auto mt-0.5 border-sky-500 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " id="select_all">
                        </label>

                        <a href="{{ route('role.index') }}" class="w-auto py-2 px-3 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <table class="min-w-full divide-y divide-gray-200 ">
                <thead class="bg-gray-50 ">
                <tr>
                     
                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Module
                            </span>
                        </div>
                    </th>



                    <th scope="col" class="px-2 py-3 text-start">
                        <div class="flex items-center gap-x-2">
                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                            Permissions
                            </span>
                        </div>
                    </th>
 
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 ">

                    @if (!empty($module_permissions))
                        @foreach ($module_permissions as $module => $module_permission_options)
                            <tr>
                                 
                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="flex items-center gap-x-3">
                                        <div class="grow">
                                            <span class="block text-sm text-gray-500 ">{{ $module }}</span>
                                        </div>
                                        </div>
                                    </div>
                                </td>



                                <td class="size-auto whitespace-nowrap">
                                    <div class="px-2 py-2">
                                        <div class="grid sm:grid-cols-4 gap-2">

                                            @if(!empty($module_permission_options) && count($module_permission_options) > 0)
                                                @foreach ($module_permission_options as $permission)
                                                    <label for="{{ $permission->name }}" class="flex p-3 w-full bg-white border border-sky-500 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500   ">
                                                        <span class="text-sm text-black ">{{ $permission->name }}</span>
                                                        <input wire:model="selected_permissions" type="checkbox" value="{{ $permission->id }}" class="shrink-0 ms-auto mt-0.5 border-sky-500 rounded text-blue-600 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none  " id="{{ $permission->name }}">
                                                    </label>
                                                @endforeach
                                            @endif

                                        </div>
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
                 
                <div class="inline-flex items-center gap-x-2">
                    <a href="{{ route('role.index') }}" class="w-auto py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:bg-red-700 disabled:opacity-50 disabled:pointer-events-none">
                        Cancel
                    </a>
                    <button type="submmit" class="w-auto py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none">
                        Update
                    </button>
                </div>


            </div>
            <!-- End Footer -->


            </div>
        </div>
        </div>
        </form>
    </div>
    <!-- End Card -->
</div>
<!-- End Table Section -->
