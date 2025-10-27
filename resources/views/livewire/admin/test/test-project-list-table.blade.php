<div class="space-y-6 p-4 sm:p-6">
    {{-- Toolbar (wire these when you hook filters/sorting) --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-semibold text-slate-900 sm:text-2xl">Project Documents</h1>
            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200">
                {{ $rows->total() }} total
            </span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <div class="relative">
                <input type="search" wire:model.live="search"
                       placeholder="Search documents..."
                       class="w-64 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm outline-none focus:border-slate-300" />
                <span class="pointer-events-none absolute right-3 top-2.5 text-slate-400">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </span>
            </div>

            {{-- Add New Button --}}
            <button
                wire:click="create"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 5v14m-7-7h14"/>
                </svg>
                Add New
            </button>


            <select wire:model.live="review" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm outline-none">
                <option value="all">All reviews</option>
                <option value="approved">Approved</option>
                <option value="in-review">In review</option>
                <option value="changes-requested">Changes requested</option>
                <option value="draft">Draft</option>
            </select>

            <select wire:model.live="sort" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm outline-none">
                <option value="updated_at_desc">Sort: Updated (desc)</option>
                <option value="submitted_at_desc">Submitted (desc)</option>
                <option value="name_asc">Name (A–Z)</option>
            </select>
        </div>
    </div>

    {{-- MOBILE: Card list --}}
    <div class="grid grid-cols-1 gap-3 sm:hidden">
        @foreach($rows as $row)
        <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm" wire:key="card-{{ $row['id'] }}">
            <div class="mb-2 flex items-center gap-1">
                <button wire:click="open({{ $row['id'] }})" class="rounded-lg bg-slate-900 px-2.5 py-1 text-xs font-medium text-white">Open</button>
                <button wire:click="download({{ $row['id'] }})" class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs">Download</button>
                <button wire:click="history({{ $row['id'] }})" class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs">History</button>
                
                <el-dropdown class=" block p-0">
                    <button class=" inline-flex rounded-md border border-slate-200 p-1 text-slate-600 hover:bg-slate-50">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4">
                            <circle cx="12" cy="5" r="2" />
                            <circle cx="12" cy="12" r="2" />
                            <circle cx="12" cy="19" r="2" />
                        </svg>

                    </button>

                    <el-menu anchor="bottom end" popover class="m-0 w-56 origin-top-right rounded-md bg-white p-0 shadow-lg outline outline-1 outline-black/5 transition [--anchor-gap:theme(spacing.2)] [transition-behavior:allow-discrete] data-[closed]:scale-95 data-[closed]:transform data-[closed]:opacity-0 data-[enter]:duration-100 data-[leave]:duration-75 data-[enter]:ease-out data-[leave]:ease-in">
                        <div class="py-1">
                            <a 
                            {{-- href="{{ route('funeral_schedule.public.show',['funeral_schedule' => $funeral_schedule->id]) }}"   --}}
                            wire:navigate
                            class="block px-4 py-2 text-sm text-gray-700 focus:bg-gray-100 focus:text-gray-900 focus:outline-none">
                                <div class="flex justify-between items-center">
                                    <div>
                                        Display
                                    </div>

                                    <div>
                                        <x-svg.display class="text-gray-600 hover:text-gray-700 size-4 shrink-0" title="Edit" />
                                    </div>
                                </div>
                            </a>
                            <a 
                            {{-- href="{{ route('funeral_schedule.show',['funeral_schedule' => $funeral_schedule->id]) }}"   --}}
                                
                            wire:navigate 
                            class="block px-4 py-2 text-sm text-gray-700 focus:bg-gray-100 focus:text-gray-900 focus:outline-none">     
                                <div class="flex justify-between items-center">
                                    <div>
                                        Details
                                    </div>

                                    <div>
                                        <x-svg.details class="text-amber-600 hover:text-amber-700 size-4 shrink-0" title="Details" />
                                    </div>
                                </div>
                            </a>
                            <a 
                            {{-- href="{{ route('funeral_schedule.edit', $funeral_schedule->id) }}"  --}}
                            wire:navigate 
                            class="block px-4 py-2 text-sm text-gray-700 focus:bg-gray-100 focus:text-gray-900 focus:outline-none">
                                <div class="flex justify-between items-center">
                                    <div>
                                        Edit
                                    </div>

                                    <div>
                                        <x-svg.edit class="text-blue-600 hover:text-blue-700 size-4 shrink-0" title="Edit" />
                                    </div>
                                </div>
                            </a>
                                
                                
                            <!-- Force Delete-->
                            <button
                                {{-- wire:click="confirmDelete({{ $funeral_schedule->id }})" --}}
                                type="button"
                                class="block w-full px-4 py-2 text-left text-sm text-gray-700 focus:bg-gray-100 focus:text-gray-900 focus:outline-none"
                            >   
                                <div class="flex justify-between items-center">
                                    <div>
                                        Delete
                                    </div>

                                    <div>
                                        <x-svg.delete class="text-red-600 hover:text-red-700 size-4 shrink-0" title="Delete" />
                                    </div>
                                </div>

                                
                            </button>

                        </div>
                    </el-menu>
                </el-dropdown>


            </div>

            <div class="flex items-start gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 ring-1 ring-slate-200">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                </div>
                <div class="min-w-0 flex-1">



                    <div class="flex flex-wrap items-center gap-2">
                        <a href="#" class="truncate text-sm font-medium text-slate-900">{{ $row['doc_name'] }}</a>
                        @include('livewire.partials.status-badge',['status' => $row['status']])
                    </div>




                    <div class="mt-0.5 text-[13px] text-slate-700">
                        <a href="#" class="font-medium hover:underline">{{ $row['project_name'] }}</a>
                        <span class="text-slate-400"> • </span>
                        <span class="text-slate-600">{{ $row['type'] }} • {{ $row['size'] }}</span>
                    </div>

                    <div>
                        <details class="group">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-2 rounded-lg bg-slate-50 px-2 py-1 text-xs text-slate-700 ring-1 ring-inset ring-slate-200 hover:bg-slate-100">
                                <span class="flex items-center gap-2">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md bg-white text-[11px] font-semibold ring-1 ring-slate-200">
                                        {{ count($row['recent']) }}
                                    </span>
                                    Recent documents
                                </span>
                                <svg class="h-3.5 w-3.5 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08z"/>
                                </svg>
                            </summary>
                            <ul class="mt-2 space-y-1 text-xs">
                                @foreach($row['recent'] as $r)
                                <li class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-2 py-1">
                                    <div class="min-w-0 truncate">
                                        <a href="#" class="font-medium text-slate-900 hover:underline">{{ $r['name'] }}</a>
                                        <span class="text-slate-500">• {{ $r['meta'] }}</span>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1">
                                        @include('livewire.partials.status-badge',['status' => $r['status']])
                                        <button class="rounded-md border border-slate-200 px-2 py-0.5">Open</button>
                                        <button class="rounded-md border border-slate-200 px-2 py-0.5">Download</button>
                                    </div>
                                </li>
                                @endforeach
                                <li class="flex items-center justify-between">
                                    <a href="#" class="text-slate-700 underline underline-offset-2">View all project documents</a>
                                </li>
                            </ul>
                        </details>
                    </div>


                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                        <span>Submitted: {{ $row['submitted_at'] }}</span>
                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                        <span>Updated: {{ $row['updated_at'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- DESKTOP: Table with sticky actions column --}}
    <div class="hidden overflow-auto rounded-2xl border border-slate-200 bg-white shadow-sm sm:block">
        <table class="min-w-full">
            <thead class="bg-slate-50">
                <tr class="text-left text-xs font-semibold text-slate-600">
                    <th class="sticky left-0 z-10 w-44 border-r border-slate-200 bg-slate-50/95 px-3 py-3 backdrop-blur">Actions</th>
                    <th class="px-4 py-3">Project</th>
                    <th class="px-4 py-3">Submitter</th>
                    <th class="px-4 py-3">Current Document</th>
                    <th class="px-4 py-3">Review Status</th>
                    <th class="px-4 py-3">Submitted</th>
                    <th class="px-4 py-3">Last Updated</th>
                    <th class="px-4 py-3">Recent</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm">
                @foreach($rows as $row)
                <tr class="group bg-white hover:bg-slate-50" wire:key="row-{{ $row['id'] }}">
                    {{-- Actions (sticky) --}}
                    <td class="sticky left-0 z-20 border-r border-slate-200 bg-white/95 px-3 py-1.5 backdrop-blur whitespace-nowrap">
                        <div class="flex items-center justify-between space-x-2">
                            <div class="flex items-center gap-1">
                                <button wire:click="open({{ $row['id'] }})" class="rounded-md bg-black px-2.5 py-1 text-xs font-medium text-white">Show</button>
                                 

                                <el-dropdown class="inline-block p-0">
                                    <button class=" inline-flex rounded-md border border-slate-200 p-1 text-slate-600 hover:bg-slate-50">
                                        
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4">
                                            <circle cx="12" cy="5" r="2" />
                                            <circle cx="12" cy="12" r="2" />
                                            <circle cx="12" cy="19" r="2" />
                                        </svg>

                                    </button>

                                    <el-menu anchor="bottom end" popover class="m-0 w-56 origin-top-right rounded-md bg-white p-0 shadow-lg outline outline-1 outline-black/5 transition [--anchor-gap:theme(spacing.2)] [transition-behavior:allow-discrete] data-[closed]:scale-95 data-[closed]:transform data-[closed]:opacity-0 data-[enter]:duration-100 data-[leave]:duration-75 data-[enter]:ease-out data-[leave]:ease-in">
                                        <div class="py-1">
                                            <a 
                                            {{-- href="{{ route('funeral_schedule.public.show',['funeral_schedule' => $funeral_schedule->id]) }}"   --}}
                                            wire:navigate
                                            class="block px-4 py-2 text-sm text-gray-700 focus:bg-gray-100 focus:text-gray-900 focus:outline-none">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        Display
                                                    </div>

                                                    <div>
                                                        <x-svg.display class="text-gray-600 hover:text-gray-700 size-4 shrink-0" title="Edit" />
                                                    </div>
                                                </div>
                                            </a>
                                            <a 
                                            {{-- href="{{ route('funeral_schedule.show',['funeral_schedule' => $funeral_schedule->id]) }}"   --}}
                                                
                                            wire:navigate 
                                            class="block px-4 py-2 text-sm text-gray-700 focus:bg-gray-100 focus:text-gray-900 focus:outline-none">     
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        Details
                                                    </div>

                                                    <div>
                                                        <x-svg.details class="text-amber-600 hover:text-amber-700 size-4 shrink-0" title="Details" />
                                                    </div>
                                                </div>
                                            </a>
                                            <a 
                                            {{-- href="{{ route('funeral_schedule.edit', $funeral_schedule->id) }}"  --}}
                                            wire:navigate 
                                            class="block px-4 py-2 text-sm text-gray-700 focus:bg-gray-100 focus:text-gray-900 focus:outline-none">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        Edit
                                                    </div>

                                                    <div>
                                                        <x-svg.edit class="text-blue-600 hover:text-blue-700 size-4 shrink-0" title="Edit" />
                                                    </div>
                                                </div>
                                            </a>
                                                
                                                
                                            <!-- Force Delete-->
                                            <button
                                                {{-- wire:click="confirmDelete({{ $funeral_schedule->id }})" --}}
                                                type="button"
                                                class="block w-full px-4 py-2 text-left text-sm text-gray-700 focus:bg-gray-100 focus:text-gray-900 focus:outline-none"
                                            >   
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        Delete
                                                    </div>

                                                    <div>
                                                        <x-svg.delete class="text-red-600 hover:text-red-700 size-4 shrink-0" title="Delete" />
                                                    </div>
                                                </div>

                                                
                                            </button>

                                        </div>
                                    </el-menu>
                                </el-dropdown>
 

                            </div>
                        </div>
                    </td>

                    {{-- Project --}}
                    <td class="px-4 py-2">
                        <div class="flex items-center gap-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 ring-1 ring-slate-200">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4 7a2 2 0 0 1 2-2h7l5 5v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M13 5v4h4"/></svg>
                            </div>
                            <div>
                                <a href="#" class="font-medium text-slate-900 hover:underline">{{ $row['project_name'] }}</a>
                                <div class="text-xs text-slate-500">Env. Impact Assessment</div>
                            </div>
                        </div>
                    </td>

                    {{-- Submitter --}}
                    <td class="px-4 py-2 text-slate-700">{{ $row['submitter_name'] }}</td>

                    {{-- Current Document --}}
                    <td class="px-4 py-2">
                        <div class="flex items-start gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 ring-1 ring-slate-200">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="#" class="truncate font-medium text-slate-900 hover:underline">{{ $row['doc_name'] }}</a>
                                <div class="text-xs text-slate-500">{{ $row['type'] }} • {{ $row['size'] }}</div>
                            </div>
                            <div class="flex items-center gap-1">
                                <button wire:click="open({{ $row['id'] }})" class="rounded-md border border-slate-200 px-2.5 py-1 text-xs">Open</button>
                                <button wire:click="download({{ $row['id'] }})" class="rounded-md border border-slate-200 px-2.5 py-1 text-xs">Download</button>
                            </div>
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-2">
                        @include('livewire.partials.status-badge',['status' => $row['status']])
                    </td>

                    {{-- Submitted / Updated --}}
                    <td class="px-4 py-2 text-slate-600">{{ $row['submitted_at'] }}</td>
                    <td class="px-4 py-2 text-slate-600">{{ $row['updated_at'] }}</td>

                    {{-- Collapsible recent documents --}}
                    <td class="px-4 py-2 text-slate-700">
                        <details class="group">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-2 rounded-lg bg-slate-50 px-2 py-1 text-xs text-slate-700 ring-1 ring-inset ring-slate-200 hover:bg-slate-100">
                                <span class="flex items-center gap-2">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-md bg-white text-[11px] font-semibold ring-1 ring-slate-200">
                                        {{ count($row['recent']) }}
                                    </span>
                                    Recent documents
                                </span>
                                <svg class="h-3.5 w-3.5 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08z"/>
                                </svg>
                            </summary>
                            <ul class="mt-2 space-y-1 text-xs">
                                @foreach($row['recent'] as $r)
                                <li class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-2 py-1">
                                    <div class="min-w-0 truncate">
                                        <a href="#" class="font-medium text-slate-900 hover:underline">{{ $r['name'] }}</a>
                                        <span class="text-slate-500">• {{ $r['meta'] }}</span>
                                    </div>
                                    <div class="flex shrink-0 items-center gap-1">
                                        @include('livewire.partials.status-badge',['status' => $r['status']])
                                        <button class="rounded-md border border-slate-200 px-2 py-0.5">Open</button>
                                        <button class="rounded-md border border-slate-200 px-2 py-0.5">Download</button>
                                    </div>
                                </li>
                                @endforeach
                                <li class="flex items-center justify-between">
                                    <a href="#" class="text-slate-700 underline underline-offset-2">View all project documents</a>
                                </li>
                            </ul>
                        </details>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="flex items-center justify-between text-sm">
        <p class="text-slate-600">Showing {{ $rows->firstItem() }}–{{ $rows->lastItem() }} of {{ $rows->total() }}</p>
        <div class="hidden sm:block">
            {{ $rows->onEachSide(1)->links() }}
        </div>
    </div>
</div>
