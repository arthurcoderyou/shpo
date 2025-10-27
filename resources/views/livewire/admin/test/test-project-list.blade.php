<div x-data="{
  selected: new Set(),
  toggle(id){ this.selected.has(id) ? this.selected.delete(id) : this.selected.add(id) },
  clear(){ this.selected.clear() },
  allChecked: false,
  checkAll(ids){ this.allChecked = !this.allChecked; this.selected = this.allChecked ? new Set(ids) : new Set(); }
}" class="space-y-6 p-4 sm:p-6">


  <!-- Toolbar -->
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
      <h1 class="text-xl font-semibold text-slate-900 sm:text-2xl">Projects</h1>
      <span class="hidden sm:inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200">42 total</span>
    </div>
    <div class="flex flex-wrap items-center gap-2">
      <div class="relative">
        <input type="search" placeholder="Search projects..." class="w-64 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm outline-none focus:border-slate-300" />
        <span class="pointer-events-none absolute right-3 top-2.5 text-slate-400">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </span>
      </div>
      <select class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm outline-none">
        <option>All statuses</option><option>Active</option><option>On hold</option><option>Completed</option>
      </select>
      <select class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm outline-none">
        <option>Sort: Updated (desc)</option><option>Name (A–Z)</option><option>Created (desc)</option>
      </select>
    </div>
  </div>



    {{-- <!-- MOBILE: Card list (sm:hidden) -->
    <div class="grid grid-cols-1 gap-3 sm:hidden">
    <!-- Example card; replace id with project ID -->
    <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm" x-data="{ open:false }">
        <!-- Compact controls: one primary + kebab -->
        <div class="mb-2 flex items-center gap-2">
        <a href="#" class="rounded-lg bg-slate-900 px-2.5 py-1 text-xs font-medium text-white">Show</a>

        <details class="relative ml-auto">
            <summary class="flex h-8 w-8 list-none items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 cursor-pointer">⋮</summary>
            <div class="absolute right-0 mt-1 w-48 rounded-lg border border-slate-200 bg-white p-1 text-sm shadow-lg z-20">
              <a class="block rounded-md px-2 py-1 hover:bg-slate-50" href="#">Edit</a>
              <a class="block rounded-md px-2 py-1 hover:bg-slate-50" href="#">Force Submit</a>
              <a class="block rounded-md px-2 py-1 hover:bg-slate-50" href="#">Approve</a>
              <a class="block rounded-md px-2 py-1 hover:bg-slate-50" href="#">Reject</a>
              <div class="my-1 h-px bg-slate-200"></div>
              <a class="block rounded-md px-2 py-1 hover:bg-slate-50" href="#">Reviewer Link</a>
              <a class="block rounded-md px-2 py-1 hover:bg-slate-50" href="#">Project Documents Link</a>
              <div class="my-1 h-px bg-slate-200"></div>
              <a class="block rounded-md bg-rose-50 px-2 py-1 text-rose-700 hover:bg-rose-100" href="#">Delete</a>
            </div>
        </details>
        </div>

        <!-- Card content -->
        <div class="flex items-start gap-3">
        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 ring-1 ring-slate-200">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4 7a2 2 0 0 1 2-2h7l5 5v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M13 5v4h4"/></svg>
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
            <a href="#" class="truncate text-sm font-medium text-slate-900">Riverbend Redevelopment</a>
            <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[10px] font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">Active</span>
            </div>

            <!-- Current document peek -->
            <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50/50 p-2">
            <div class="mb-1 flex items-center justify-between gap-2">
                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-600">Current document</p>
                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Approved</span>
            </div>
            <div class="flex items-start gap-2">
                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-white ring-1 ring-slate-200">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                <a href="#" class="truncate text-[13px] font-medium text-slate-900">EIA_Section4_Mitigation.pdf</a>
                <div class="text-[11px] text-slate-600">Submitted: Aug 29, 2025 • 14:18</div>
                </div>
                <div class="flex items-center gap-1">
                <a href="#" class="rounded-lg border border-slate-200 px-2.5 py-1 text-[11px]">Open</a>
                <a href="#" class="rounded-lg border border-slate-200 px-2.5 py-1 text-[11px]">Download</a>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>

    <!-- Repeat cards... -->
    </div> --}}




  <!-- DESKTOP TABLE -->
<div class="block overflow-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
  <table class="min-w-full">
    <thead class="bg-slate-50">
      <tr class="text-left text-xs font-semibold text-slate-600">
        <!-- Sticky checkbox column -->
         
        <!-- Sticky actions column (shifted by checkbox width) -->
        <th class="sticky left-0 z-20 w-48 border-r border-slate-200 bg-slate-50/95 px-3 py-3 backdrop-blur">Actions</th>

        <th class="px-4 py-3 text-nowrap">Project</th>
        <th class="px-4 py-3 text-nowrap">Owner</th>
        <th class="px-4 py-3 text-nowrap">Current Document</th>
        <th class="px-4 py-3 text-nowrap">Review Status</th>
        <th class="px-4 py-3 text-nowrap">Submitted</th>
        <th class="px-4 py-3 text-nowrap">Last Updated</th>
        <th class="px-4 py-3 text-nowrap">Documents</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-200 text-sm">
        <!-- Row (use :key / loop in Blade/Livewire) -->
        <tr class="group bg-white hover:bg-slate-50">
            <!-- Checkbox (sticky left) -->
            {{-- <td class="sticky left-0 z-10 border-r border-slate-200 bg-white/95 px-3 py-1.5 backdrop-blur">
            <input type="checkbox" @change="toggle(1)" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-500">
            </td> --}}

            <!-- Compact actions (sticky next to checkbox) -->
            <td class="sticky left-0 z-20 border-r border-slate-200 bg-white/95 px-3 py-1.5 backdrop-blur whitespace-nowrap hover:bg-slate-50">
              <div class="flex items-center align-middle align-items-center justify-between space-x-2">

                  <div>
                      <a href="#" class="rounded-md border border-slate-200 px-2.5 py-1 text-xs mx-1 my-1 bg-black text-white">Show</a>
                  </div>
                    
                  
                  <el-dropdown class="inline-block p-0">
                      <button class="  w-full   rounded-md bg-white   text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                          
                          {{-- <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="  size-5 text-gray-400">
                          <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                          </svg> --}}
                          <x-svg.dots-horizontal class="text-gray-500 hover:text-gray-700 m-1" title="More options" />
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

            </td>

            <!-- Project -->
            <td class="px-4 py-2">
              <div class="flex items-center gap-2">
                  <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 ring-1 ring-slate-200">
                  <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4 7a2 2 0 0 1 2-2h7l5 5v9a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/><path d="M13 5v4h4"/></svg>
                  </div>
                  <div>
                  <a href="#" class="font-medium text-slate-900 hover:underline">Riverbend Redevelopment</a>
                  <div class="text-xs text-slate-500">Env. Impact Assessment</div>
                  </div>
              </div>
            </td>

            <td class="px-4 py-2 text-slate-700">Jane Doe</td>

            <!-- Current Document -->
            <td class="px-4 py-2">
              <div class="flex items-start gap-2">
                  <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-50 ring-1 ring-slate-200">
                  <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                  </div>
                  <div class="min-w-0 flex-1">
                  <a href="#" class="truncate font-medium text-slate-900 hover:underline">EIA_Section4_Mitigation.pdf</a>
                  <div class="text-xs text-slate-500">PDF • 2.4 MB</div>
                  </div>
                  <div class="flex items-center gap-1">
                  <a href="#" class="rounded-md border border-slate-200 px-2.5 py-1 text-xs">Open</a>
                  <a href="#" class="rounded-md border border-slate-200 px-2.5 py-1 text-xs">Download</a>
                  </div>
              </div>
            </td>

            <td class="px-4 py-2"><span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Approved</span></td>
            <td class="px-4 py-2 text-slate-600">Aug 29, 2025 • 14:18</td>
            <td class="px-4 py-2 text-slate-600">Sep 30, 2025 • 16:40</td>

            <!-- Collapsible document list -->
            <td class="px-4 py-2 text-slate-700">
                <details class="group">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-2 rounded-lg bg-slate-50 px-2 py-1 text-xs text-slate-700 ring-1 ring-inset ring-slate-200 hover:bg-slate-100">
                    <span class="flex items-center gap-2">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-md bg-white text-[11px] font-semibold ring-1 ring-slate-200">12</span>
                        Recent documents
                    </span>
                    <svg class="h-3.5 w-3.5 transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor"><path d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08z"/></svg>
                    </summary>
                    <ul class="mt-2 space-y-1 text-xs">
                    <li class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-2 py-1">
                        <div class="min-w-0 truncate"><a href="#" class="font-medium text-slate-900 hover:underline">EIA_Section4_Mitigation.pdf</a> <span class="text-slate-500">• PDF • 2.4 MB</span></div>
                        <div class="flex shrink-0 items-center gap-1">
                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Approved</span>
                        <a href="#" class="rounded-md border border-slate-200 px-2 py-0.5">Open</a>
                        <a href="#" class="rounded-md border border-slate-200 px-2 py-0.5">Download</a>
                        </div>
                    </li>
                    <li class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-2 py-1">
                        <div class="min-w-0 truncate"><a href="#" class="font-medium text-slate-900 hover:underline">Sampling_Data_Q1_2024.xlsx</a> <span class="text-slate-500">• XLSX • 1.1 MB</span></div>
                        <div class="flex shrink-0 items-center gap-1">
                        <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700">Changes requested</span>
                        <a href="#" class="rounded-md border border-slate-200 px-2 py-0.5">Open</a>
                        <a href="#" class="rounded-md border border-slate-200 px-2 py-0.5">Download</a>
                        </div>
                    </li>
                    <li class="flex items-center justify-between">
                        <a href="#" class="text-slate-700 underline underline-offset-2">View all project documents</a>
                    </li>
                    </ul>
                </details>
            </td>
        </tr>

       

      </tbody>
    </table>
  </div>


 <!-- Sticky bulk bar -->
<div x-show="selected.size > 0"
     x-transition
     class="sticky bottom-3 z-30 mx-auto flex w-full max-w-5xl items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white/95 px-3 py-2 shadow-lg backdrop-blur">
  <div class="flex items-center gap-2 text-sm text-slate-700">
    <span class="inline-flex h-7 min-w-7 items-center justify-center rounded-md bg-slate-900 px-2 text-xs font-semibold text-white" x-text="selected.size"></span>
    selected
  </div>
  <div class="flex flex-wrap items-center gap-1">
    <button class="rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs text-emerald-700">Bulk Approve</button>
    <button class="rounded-md border border-amber-200 bg-amber-50 px-2.5 py-1 text-xs text-amber-700">Bulk Reject</button>
    <button class="rounded-md border border-slate-200 px-2.5 py-1 text-xs">Bulk Force Submit</button>
    <button class="rounded-md border border-slate-200 px-2.5 py-1 text-xs">Open Reviewer Links</button>
    <button class="rounded-md border border-slate-200 px-2.5 py-1 text-xs">Open Documents</button>
    <button @click="clear()" class="rounded-md border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs text-rose-700">Clear</button>
  </div>
</div>

<!-- Close the wrapper started at top -->
</div>

