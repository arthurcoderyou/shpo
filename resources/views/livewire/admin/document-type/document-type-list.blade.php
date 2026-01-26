<!-- Table Section -->
<div class="max-w-full px-4 py-6 sm:px-6 lg:px-8  mx-auto">


    @if(Auth::user()->can('system access global admin') || Auth::user()->can('document type create') )
    <div wire:key="documentTypeForm" class="grid grid-cols-12 gap-3 items-end mb-2">

    

    


        <div class="col-span-12 sm:cols   md:col-span-8 " >
        

            <x-ui.input
                id="name"
                label="Add New Document type"
                wire:model.live="name"
                required
                placeholder="Enter name of the new document"
                {{-- help="Use the official title from the submission." --}}
                :error="$errors->first('name')"
                type="text" 
                displayTooltip
                position="right"
                tooltipText="Enter the new document name"
                
            />
        </div>
 


        <div class="col-span-6 md:col-span-2">
            <!-- Add selected to the main assigned reviewers list -->
            <button type="button" wire:click="add"
                    class="w-full text-nowrap py-2.5 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg bg-sky-600 text-white hover:bg-sky-700">
                Add to Table
            </button>
        </div>
        

    
        <div class="col-span-6 md:col-span-2 flex gap-2">
            <!-- Add selected to the main assigned reviewers list -->
            <button type="button" wire:click="save()"
                    class="w-full text-nowrap py-2.5 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                Save All
            </button>

            <x-ui.table.reset-button wireClick="resetFilters" />

        </div>
        

        
    </div>
    @endif



    <!-- Draggable table (rows identified by row_uid) -->
    <div
        x-data="{ 
        roleColors: {
            'global admin': 'bg-red-100 text-red-700 ring-red-200',
            'admin':        'bg-amber-100 text-amber-800 ring-amber-200',
            'reviewer':     'bg-sky-100 text-sky-700 ring-sky-200',
            'user':         'bg-slate-100 text-slate-700 ring-slate-200',
            '__none':       'bg-zinc-100 text-zinc-700 ring-zinc-200',
        }, 

        draggingUid: null,
        start(e,uid){ this.draggingUid = uid; e.dataTransfer.effectAllowed='move' },
        over(e){ e.preventDefault(); e.dataTransfer.dropEffect='move' },
        drop(e, targetUid){
            e.preventDefault();
            if(this.draggingUid===null || this.draggingUid===targetUid) return;
            const rows = Array.from($el.querySelectorAll('[data-row]')).map(r => r.dataset.uid);
            const from = rows.indexOf(this.draggingUid);
            const to   = rows.indexOf(targetUid);
            rows.splice(to, 0, rows.splice(from,1)[0]);
            @this.reorder( rows);
            this.draggingUid = null;
        }
        }" 
        class="bg-white rounded-xl border shadow-sm overflow-hidden"
    >
    

        <table class="min-w-full">
        <thead class="bg-slate-50">
            <tr>
                <th class="w-16 px-4 py-2 text-left text-xs font-semibold text-slate-600">Order</th>
            <th class="  px-4 py-2 text-left text-xs font-semibold text-slate-600">Name</th>
             <th class="  px-4 py-2 text-left text-xs font-semibold text-slate-600">Connected Documents</th>
            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Modified</th>  
            <th class="w-24 px-4 py-2"></th>
            </tr>
        </thead>

        {{-- LOADING BODY (shown only while those actions run) --}}
        <tbody
            class="divide-y divide-slate-200"
            wire:loading
            wire:target="addSelected,addOpenSlots,remove,currentTypeId"
        >
            {{-- optional single-row announcement for screen readers --}}
            <tr>
            <td colspan="4" class="sr-only" role="status">Loading document types </td>
            </tr>

            {{-- skeleton rows --}}
            @for ($i = 0; $i < max(min(count($assigned), 6), 3); $i++)
            <tr class="bg-white">
                <td class="px-4 py-3">
                <div class="h-4 w-10 rounded animate-pulse bg-slate-200"></div>
                </td>
                <td class="px-4 py-3">
                <div class="h-4 w-48 rounded animate-pulse bg-slate-200 mb-1"></div>
                <div class="h-3 w-24 rounded animate-pulse bg-slate-100"></div>
                </td>
                <td class="px-4 py-3">
                <div class="flex gap-2">
                    <div class="h-5 w-16 rounded-full animate-pulse bg-slate-200"></div>
                    <div class="h-5 w-14 rounded-full animate-pulse bg-slate-200"></div>
                    <div class="h-5 w-20 rounded-full animate-pulse bg-slate-200 hidden sm:block"></div>
                </div>
                </td>
                <td class="px-4 py-3 text-right">
                <div class="inline-flex items-center gap-2">
                    <div class="h-8 w-20 rounded-lg animate-pulse bg-slate-200"></div>
                </div>
                </td>
            </tr>
            @endfor
        </tbody>


        <tbody
        wire:loading.remove
        wire:target="addSelected,addOpenSlots,remove,currentTypeId"
        class="divide-y divide-slate-200">
            @forelse($assigned as $row)
    
            <tr
                wire:key="reviewer-row-{{ $row['row_uid'] }}"   {{-- IMPORTANT --}}
                data-row 
                x-data="{
                    {{-- roles:@js($row['roles'] ?? []), --}}
                    {{-- userId:@js($row['user_id'] ?? null), --}}

                    openEdit: false,
                    updated_name: '{{ $row['name'] }}', 

                    update(rowId,updated_name){
                        
                        if(this.rowId===null ) return;
                        
                        @this.update_row( rowId,updated_name); 
                        openEdit=false;

                    }

                }"
    

                data-uid="{{ $row['row_uid'] }}"
                {{-- Only make middle rows draggable --}}
                
                    draggable="true"
                    @dragstart="start($event, '{{ $row['row_uid'] }}')"
                    @dragover="over($event)"
                    @drop="drop($event, '{{ $row['row_uid'] }}')"
                
                class="bg-white hover:bg-slate-50"
                
            >
                <td class="px-4 py-2">
                <div class="flex items-center gap-2"> 
    
                        <svg class="w-4 h-4 text-nowrap text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 9h2v2H7V9zm4 0h2v2h-2V9zM7 14h2v2H7v-2zm4 0h2v2h-2v-2z"/></svg>
                    
                
                    <span class="text-sm text-slate-700">{{ $row['order'] }}</span>
                </div>
                </td>

                <td class="px-4 py-2">
                    <div class="flex items-center gap-2"> 
        
                        
                    
                        <span class="text-sm text-slate-700">{{ $row['name'] }}</span>
                    </div>

                    

                </td>

                <td class="px-4 py-2"> 

                    <div class="flex items-center gap-2"> 
        
                        <span class="text-sm text-slate-700">{{ $row['document_count'] }}</span>
                    </div>

                </td>


                <!-- Review Period -->
                <td class="px-4 py-2"> 
                    <span class="text-sm text-slate-800  "> {{ \Carbon\Carbon::parse( $row['updated_at'])->format('d M, H:i') }}</span> 
                </td>

    

                <td class="px-4 py-2 text-right flex space-x-2"
                >
    

                    <button
                        type="button"
                        @click="openEdit = true; 
                        "
                        class="px-2 py-1 text-sm rounded-md bg-sky-50 text-sky-600 hover:bg-sky-100"
                    >
                        
                        Edit
                    </button>

                    <!-- ======================== -->
                    <!-- Edit -->
                    <!-- ======================== -->
                    <div
                    
                        x-show="openEdit"
                        x-cloak
                        x-transition.opacity
                        @keydown.escape.window="openEdit = false"
                        @click.self="openEdit = false"
                        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-4"
                        aria-modal="true" role="dialog"
                    >
                        <!-- Modal box -->
                        <div
                            x-transition
                            @click.stop
                            class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden"
                        >
                            <!-- Header -->
                            <div class="flex items-center justify-between border-b bg-sky-50 px-5 py-3">
                                <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-sky-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 4v6h6M20 20v-6h-6M20 4h-6M4 20h6" />
                                    </svg>
                                    Edit Document type
                                </h3>
                                <button
                                    @click="openEdit = false"
                                    class="text-slate-500 hover:text-slate-700 transition"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Body -->
                            <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
                                
                                <div class=" " >
                                    

                                    <x-ui.input
                                    id="{{ $row['row_uid'] }}_updated_name"
                                    label="Document type name"
                                    x-model="updated_name"
                                    required
                                    placeholder="Document type name"
                                    {{-- help="Use the official title from the submission." --}}
                                    :error="$errors->first('updated_name')"
                                    type="text" 
                                    displayTooltip
                                    position="right"
                                    tooltipText="Update the document type name"
                                    
                                    />
                                </div>


                            {{-- <div class="col-span-6 md:col-span-2" >
                                <x-ui.select
                                id="{{ $row['row_uid'] }}_period_unit"
                                label="Period Unit"
                                x-model="updated_period_unit"
                                placeholder="Select a unit"
                                :options="$period_unit_options"
                                :error="$errors->first('updated_period_unit')"
                                displayTooltip="true"
                                position="right"
                                tooltipText="Set the review period unit (day, week, month)"
                                />
                            </div> --}}

    
    
                                
                            </div>

                            <!-- Footer -->
                            <div class="border-t px-5 py-3 flex justify-end gap-2 bg-slate-50">
                                <button
                                    type="button"
                                    @click="openEdit = false, open=false"
                                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="button"
                                    @click="update('{{ $row['row_uid'] }}',updated_name);openEdit=false"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 transition disabled:opacity-50"
                                >
                                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 018-8" />
                                    </svg>
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
    

                {{-- @endif --}}

    
                

                <button type="button"
                        wire:click="remove('{{ $row['row_uid'] }}')"
                        class="px-2 py-1 text-sm rounded-md bg-rose-50 text-rose-600 hover:bg-rose-100">
                    Remove
                </button>
    


                
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-6 text-sm text-slate-500 text-center">
                No reviewers yet for this document type.
                </td>
            </tr>
            @endforelse
        </tbody>

        </table>
    </div>


    <!-- Loaders -->
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
    <!-- ./ Loaders -->


</div>
<!-- End Table Section -->
