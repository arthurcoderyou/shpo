<!-- resources/views/livewire/reviewer-board-per-doc-type.blade.php -->
<!-- Table Section -->
<div class="max-w-full px-4 py-6 sm:px-6 lg:px-8  mx-auto"
     x-data="{ 
        pageModeFilter: @entangle('page_mode'),
        
         
    }"

  

>

    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-2 max-w-full overflow-x-auto mb-2">
        <div class="flex items-center gap-x-2">  

            {{-- @if($page_mode == "project") --}}
            <button 
                @click="pageModeFilter = 'project', $wire.set('page_mode', 'project')"
                :class="pageModeFilter === 'project' ? 'bg-blue-500 text-white' : 'bg-white text-blue-600 border border-blue-300'"
                class="text-nowrap px-3 py-1 rounded-lg text-sm font-medium transition flex items-center space-x-1"
            >
                
                <span class="hidden lg:block">RC Reviewers</span>  <!-- For Desktop view-->
                <span class="block lg:hidden">RC</span>    <!-- For Mobile view--> 


            </button> 
            {{-- @else --}}
            <button 
                @click="pageModeFilter = 'document', $wire.set('page_mode', 'document')"
                :class="pageModeFilter === 'document' ? 'bg-yellow-500 text-white' : 'bg-white text-yellow-600 border border-yellow-300'"
                class="text-nowrap px-3 py-1 rounded-lg text-sm font-medium transition flex items-center space-x-1"
            >
                
                <span class="hidden lg:block">Document Reviewers</span>  <!-- For Desktop view-->
                <span class="block lg:hidden">Document</span>    <!-- For Mobile view--> 


            </button>
            {{-- @endif --}}


        </div> 
    </div>

    @if($page_mode == "document")
    <!-- Document Type Selector -->
    <div class="flex gap-3 items-end mb-2">
        {{-- <div class="grow">
          <label class="block text-sm font-medium text-gray-800 ">Document</label>
          <select
              wire:model.live="currentTypeId"
              class="w-full py-2.5 px-3 rounded-lg border border-gray-300 text-sm focus:ring-sky-500 focus:border-sky-500"
          > 
            @if(!empty($documentTypes))
              @foreach($documentTypes as $t)
              <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
              @endforeach
            @else
              <option value="">No documents added for this project</option>
            @endif
          </select>
        </div> --}}

        <div class="grow ">
          <label class="block text-sm font-medium text-gray-800 mb-2">
              Document
          </label>

          <div class="w-full py-2 px-3 rounded-lg border border-gray-300 text-sm focus:ring-sky-500 focus:border-sky-500">
            <div class="flex flex-wrap gap-2">
              @forelse($project->project_documents as $project_document)
                  <a
                      href="{{ route('project.document.reviewer.index', [
                          'project_document' => $project_document->id ,
                      ]) }}"

                      wire:navigate
                      class="px-4 py-1 text-sm rounded-lg border
                            {{ request()->route('project_document') == ($project_document->id ) || 
                                (!empty($project_document_id) && ($project_document->id == $project_document_id))

                                  ? 'bg-sky-600 text-white border-sky-600'
                                  : 'bg-white text-gray-700 border-gray-300 hover:bg-sky-50 hover:border-sky-400' }}"
                  >
                      {{ $project_document->document_type->name ?? 'Unnamed Document' }}
                  </a>
              @empty
                  <span class="text-sm text-gray-500">
                      No documents added for this project.
                  </span>
              @endforelse
            </div>
          </div>
          
        </div>



        @if( Auth::user()->can('system access global admin') || Auth::user()->hasPermissionTo('project reviewer edit') )
          <div x-data="{ open:false }" class="shrink-0">
              <!-- Trigger -->
              <button type="button"
                      @click="open = true"
                      class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60"
                      wire:loading.attr="disabled"
                      wire:target="save">
                  Save All
              </button>

              <!-- Backdrop -->
              <div x-show="open"
                  x-transition.opacity
                  class="fixed inset-0 z-40 bg-black/40"
                  @keydown.escape.window="open = false"
                  aria-hidden="true"></div>

              <!-- Modal -->
              <div x-show="open"
                  x-transition
                  class="fixed inset-0 z-50 flex items-center justify-center p-4">
                  <div @click.away="open = false"
                    class="w-full max-w-md rounded-xl bg-white shadow-xl ring-1 ring-black/5">
                    <!-- Header -->
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h2 class="text-base font-semibold text-slate-800">Confirm Save</h2>
                    </div>

                    <!-- Body -->
                    <div class="px-5 py-4 space-y-3 text-sm text-slate-700">
                        <p>Some reviewers cannot be removed because they are already assigned.</p>
                        <p class="font-medium">Are you sure you want to save these records?</p>
                    </div>

                    <!-- Footer -->
                    <div class="px-5 py-4 flex items-center justify-end gap-2 border-t border-slate-200">
                        <button type="button"
                                @click="open = false"
                                class="px-3.5 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                        Cancel
                        </button>
                        <button type="button"
                                @click="$wire.save(); open = false"
                                class="px-3.5 py-2 rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60"
                                wire:loading.attr="disabled"
                                wire:target="save">
                        Yes, Save
                        </button>
                    </div>
                  </div>
              </div>
          </div>
        @endif
    </div>
    @endif
    
    

    
    @php
        $typeId   = (int) $currentTypeId;
        // $options  = $optionsByType[$typeId]  ?? [];
        $selected = $selectedByType[$typeId] ?? [];
        $assigned = $assignedByType[$typeId] ?? [];
    @endphp 

    @if(!empty($documentTypes) && $page_mode == "document")
      @if( Auth::user()->can('system access global admin') || Auth::user()->hasPermissionTo('project reviewer edit') )
      <!-- Multi-select + Times to add -->
      <div wire:key="type-{{ $typeId }}" class="grid grid-cols-12 gap-3 items-end mb-2">
        <div 
        class="col-span-12 md:col-span-6" 
        
        >
          <label class="inline-block text-sm font-medium text-gray-800">
              Add reviewers 
              
              ({{ $documentTypes[array_search($typeId, array_column($documentTypes,'id'))]['name'] ?? '' }})
              
          </label>

          <x-ui.reviewer.multi-select-search
            :id="'selectedByType.' . $typeId"
            :options="$options"
            :entangle="'selectedByType.' . $typeId"
            label="Select reviewers  "
        />
        </div>


          {{-- <!-- Times to add -->
          <div class="col-span-6 md:col-span-2">
          <label class="block text-sm font-medium text-gray-800 ">Times to add</label>
          <input type="number" min="1" max="20"
                  wire:model.lazy="repeatByType.{{ $typeId }}"
                  class="w-full py-2.5 px-3 rounded-lg border border-gray-300 text-sm focus:ring-sky-500 focus:border-sky-500"
                  placeholder="1">
          </div> --}}

          <div class="col-span-6 md:col-span-2">
            <x-ui.input
              id="period_value"
              label="Review Period"
              wire:model.live="period_value"
              required
              placeholder="Estimated days of review Period"
              {{-- help="Use the official title from the submission." --}}
              :error="$errors->first('period_value')"
              type="number"
              min="0"
              max="100"
              displayTooltip
              position="right"
              tooltipText="Set the estimated days of review period"
              
            />
          </div>

          <div class="col-span-6 md:col-span-4">
            <!-- Add selected to the main assigned reviewers list -->
            <button type="button" wire:click="addSelected"
                    class="w-full py-2.5 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg bg-sky-600 text-white hover:bg-sky-700">
                Add to Table
            </button>
          </div>

          {{-- <div class="col-span-6 md:col-span-2">
            <!-- Add selected to the main assigned reviewers list -->
            <button type="button" wire:click="addOpenSlots('admin')"
                    class="w-full py-2.5 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                Add Admin Review
            </button>
          </div> --}}

          
      </div>
      @endif
    @endif


  {{-- <div class="grid grid-cols-12 gap-3 items-end mb-2">
    <div class="col-span-6 md:col-span-4 ">
      <label class="block text-sm font-medium text-gray-800 ">Open slot for role</label>
      <select wire:model="openRoleByType.{{ $typeId }}"
              class="w-full py-2.5 px-3 rounded-lg border border-gray-300 text-sm focus:ring-sky-500 focus:border-sky-500">
        <option value="reviewer">Reviewer</option>
        <option value="admin">Admin</option>
      </select>
    </div>

    <div class="col-span-6 md:col-span-3 ">
      <label class="block text-sm font-medium text-gray-800 ">Times to add</label>
      <input type="number" min="1" max="20"
            wire:model.lazy="openRepeatByType.{{ $typeId }}"
            class="w-full py-2.5 px-3 rounded-lg border border-gray-300 text-sm focus:ring-sky-500 focus:border-sky-500"
            placeholder="1">
    </div>

    <div class="col-span-12 md:col-span-5 ">
      <button type="button" wire:click="addOpenSlots"
              class="w-full py-2.5 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">
        Add Open Slot(s)
      </button>
    </div>




  </div> --}}


  

  <!-- Draggable table (rows identified by row_uid) -->
  <div
    x-data="{
      options:@js($options),
      roleColors: {
        'global admin': 'bg-red-100 text-red-700 ring-red-200',
        'admin':        'bg-amber-100 text-amber-800 ring-amber-200',
        'reviewer':     'bg-sky-100 text-sky-700 ring-sky-200',
        'user':         'bg-slate-100 text-slate-700 ring-slate-200',
        '__none':       'bg-zinc-100 text-zinc-700 ring-zinc-200',
      },
      
       
      labelFor(id){
        const nid = Number(id);
        const o = this.options.find(o => Number(o.id) === nid);
        return o ? o.name : id;
      },
      rolesFor(id){
        const nid = Number(id);
        const o = this.options.find(o => Number(o.id) === nid);
        return o ? (o.roles ?? []) : [];
      },
      badgeCls(role){
        const key = (role || '').toLowerCase();
        const base = 'px-1.5 py-0.5 rounded-md text-xs ring-1';
        return `${base} ${(this.roleColors[key] ?? this.roleColors['__none'])}`;
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
        @this.reorder({{ $typeId }}, rows);
        this.draggingUid = null;
      }
    }"
    wire:key="table-{{ $typeId }}"
    class="bg-white rounded-xl border shadow-sm overflow-hidden"
  >
  

    <table class="min-w-full">
      <thead class="bg-slate-50">
        <tr>
          <th class="w-16 px-4 py-2 text-left text-xs font-semibold text-slate-600">Order</th>
          <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Reviewer</th>
          <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Review</th> 
          <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Review Period</th> 
          <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Roles</th>
          <th class="w-24 px-4 py-2"></th>
        </tr>
      </thead>

      {{-- LOADING BODY (shown only while those actions run) --}}
      <tbody
        class="divide-y divide-slate-200"
        wire:loading
        wire:target="addSelected,addOpenSlots,remove"
      >
        {{-- optional single-row announcement for screen readers --}}
        <tr>
          <td colspan="4" class="sr-only" role="status">Loading reviewers…</td>
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
            <td class="px-4 py-3">
              <div class="flex gap-2">
                <div class="h-5 w-16 rounded-full animate-pulse bg-slate-200"></div>
                <div class="h-5 w-14 rounded-full animate-pulse bg-slate-200"></div>
                <div class="h-5 w-20 rounded-full animate-pulse bg-slate-200 hidden sm:block"></div>
              </div>
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
      wire:target="addSelected,addOpenSlots,remove"
      class="divide-y divide-slate-200">
        @forelse($assigned as $row)

          @php
            $isFirst = $loop->first;
            $isLast = $loop->last;
          @endphp

          <tr
            wire:key="reviewer-row-{{ $row['row_uid'] }}"   {{-- IMPORTANT --}}
            data-row 
            x-data="{
              {{-- roles:@js($row['roles'] ?? []), --}}
              {{-- userId:@js($row['user_id'] ?? null), --}}
                openReview: false,
                openRespond: false,
                openEdit: false,
                updated_period_value: '{{ $row['period_value'] }}',
                updated_period_unit: '{{ $row['period_unit'] }}',
                updated_user_id: '{{ $row['user_id'] }}', 
                selected: @js($selected),

                update(rowId,period_value,period_unit,updated_user_id){
                  
                  if(this.rowId===null ) return;
                  
                  {{-- @this.update_row({{ $typeId }}, rowId,period_value,period_unit); --}}
                  @this.update_row({{ $typeId }}, rowId,period_value,'day',updated_user_id);
                  openEdit=false;

                },


                reviewStatusColors: {
                    'approved': 'rounded-full px-2 py-0.5 text-[11px] font-medium bg-lime-100 text-lime-500 capitalize', 
                    'rejected': 'rounded-full px-2 py-0.5 text-[11px] font-medium bg-red-100 text-red-500 capitalize',
                    'pending': 'rounded-full px-2 py-0.5 text-[11px] font-medium bg-amber-100 text-amber-500 capitalize',

                    'changes_requested': 'rounded-full px-2 py-0.5 text-[11px] font-medium bg-yellow-800 text-white capitalize',
                    'reviewed': 'rounded-full px-2 py-0.5 text-[11px] font-medium bg-green-100 text-green-500 capitalize',
                },
 


            }"


            data-uid="{{ $row['row_uid'] }}"
            
            {{-- draggable="true"
            @dragstart="start($event, '{{ $row['row_uid'] }}')"
            @dragover="over($event)"
            @drop="drop($event, '{{ $row['row_uid'] }}')" --}}

            {{-- Only make middle rows draggable --}}
            @unless($isFirst || $isLast || $row['status'] == true || $row['review_status'] == "reviewed" || $row['review_status'] == "approved")
                draggable="true"
                @dragstart="start($event, '{{ $row['row_uid'] }}')"
                @dragover="over($event)"
                @drop="drop($event, '{{ $row['row_uid'] }}')"
            @endunless

            class="bg-white hover:bg-slate-50"

          >
            <td class="px-4 py-2">
              <div class="flex items-center gap-2"> 

                 @if($isFirst || $isLast || $row['status'] == true || $row['review_status'] == "reviewed" || $row['review_status'] == "approved")
                    <svg class="w-4 h-4 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a4 4 0 00-4 4v2H5a1 1 0 00-1 1v8a1 1 0 001 1h10a1 1 0 001-1v-8a1 1 0 00-1-1h-1V6a4 4 0 00-4-4z"/>
                    </svg>
                @else
                    <svg class="w-4 h-4 text-nowrap text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 9h2v2H7V9zm4 0h2v2h-2V9zM7 14h2v2H7v-2zm4 0h2v2h-2v-2z"/></svg>
                @endif
 
                <span class="text-sm text-slate-700">#{{ $row['order'] }}</span>
              </div>
            </td>

            <!-- Reviewer -->
            <td class="px-4 py-2">

              
              @if(($row['slot_type'] ?? 'person') === 'person')
                <div class="flex items-center gap-2">
                  <span class="text-sm text-slate-800">{{ $row['name'] }}</span>
                    @if($row['status'] == true)
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                            Current reviewer
                        </span>
                    @endif
                </div>
              @else
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium
                        {{ ($row['slot_role'] ?? 'reviewer') === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-indigo-100 text-indigo-800' }}">
                        {{-- Open • {{ ucfirst($row['slot_role']) }} --}}
                        Open Admin Review
                    </span>

                    @if($row['status'] == true)
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                            Current reviewer
                        </span>
                    @endif

                    {{-- @if(!empty($row['user_id']) && !empty($row['name']))
                        <span class="text-xs text-slate-500">claimed by</span>
                        <span class="text-sm text-slate-800">{{ $row['name'] }}</span>
                    @else
                        <span class="text-xs text-slate-500">not claimed</span>
                    @endif --}}
                </div>
              @endif 


              {{-- {{ $row['project_reviewer_id'] ?? "N\A" }} --}}


            </td>


             <!-- Review Status -->
            <td class="px-4 py-2">
                 @php    
                    $config = $this->returnStatusConfig($row['review_status']);  
                  @endphp

               
                <div class="flex items-center gap-2">
                  {{-- <span :class="reviewStatusColors['{{ $row['review_status'] }}']">{{ $row['review_status'] }}</span> --}}

                  <span class="inline-flex items-center gap-1 rounded-full {{ $config['bg'] }} px-2 py-0.5 text-[11px] font-semibold {{ $config['text'] }} ring-1 ring-inset {{ $config['ring'] }}">
                      {{ $config['label'] }}  
                  </span>

                     
                </div>
              
            </td>


            <!-- Review Period -->
            <td class="px-4 py-2"> 
              <span class="text-sm text-slate-800 capitalize">{{ $row['period_value'] }} {{ $row['period_unit'] }}(s)</span> 
            </td>


            <!-- Roles -->
            <td class="px-4 py-2">
              <div class="flex flex-wrap items-center gap-1.5">

                @php $uid = $row['user_id'] ?? null; @endphp

                <!-- If this row has a userId (person or claimed open slot), show that user's roles -->
                @if(!empty($row['roles']))
                  <div class="flex flex-wrap items-center gap-1.5">
                    @foreach ($row['roles'] as $role)
                        <span :class="badgeCls('{{ $role }}')"  >{{ $role }}</span>
                    @endforeach
                  </div>
                @else
                   <span :class="badgeCls('')">No role</span>
                @endif


                {{-- <template x-if="userId">
                  <div class="flex flex-wrap items-center gap-1.5">
                    <template x-for="role in rolesFor(userId)" :key="role" >
                      <span :class="badgeCls(role)" x-text="role"></span>
                    </template>
                    <template x-if="rolesFor(userId).length === 0">
                      <span :class="badgeCls('')">No role</span>
                    </template>
                  </div>
                </template>

                <!-- If open & not claimed, show neutral -->
                <template x-if="!userId">
                  <span :class="badgeCls('')">No role</span>
                </template> --}}


              </div>
            </td>

            {{-- <td class="px-4 py-2 text-right"> 
                @php 
                   
                    $review_count = App\Models\Review::returnReviewCount($row['project_reviewer_id']);
                @endphp 
                @if(empty($review_count) || $review_count > 0)
                    <button type="button"
                            wire:click="remove('{{ $row['row_uid'] }}', {{ $typeId }})"
                            class="px-2 py-1 text-sm rounded-md bg-rose-50 text-rose-600 hover:bg-rose-100">
                        Remove
                    </button>
                @else
                    <span class="px-2 py-1 text-sm rounded-md  font-bold bg-gray-50 ">Locked</span>
                @endif 

            </td> --}}


            <!-- Actions -->
            <td class="px-4 py-2 text-right flex justify-end space-x-2"
            >
              @if($row['status'] == true && !empty($row['project_reviewer_id']) && $row['review_status'] == "pending")
              <!-- ======================== -->
              <!-- Review -->
              <!-- ======================== -->
                <button
                    type="button"
                    @click="openReview = true; 
                      "
                    @if($isFirst  )
                      disabled
                    @endif

                    class="px-2 py-1 text-sm rounded-md bg-green-50 text-green-600 hover:bg-green-100
                    @if($isFirst  )
                      cursor-not-allowed  pointer-events-none
                        cursor-not-allowed opacity-70 
                    @endif
                    "
                >
                    
                  Review
                </button>
                <div
                    
                    x-show="openReview"
                    x-cloak
                    x-transition.opacity
                    @keydown.escape.window="openReview = false"
                    @click.self="openReview = false"
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
                        <div class="flex items-center justify-between border-b bg-green-50 px-5 py-3">
                            <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 4v6h6M20 20v-6h-6M20 4h-6M4 20h6" />
                                </svg>
                                Submit a review
                            </h3>
                            <button
                                @click="openReview = false"
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
                              @php
                              $project_reviewer_id =   $row['project_reviewer_id'];
                              @endphp
                             <livewire:components.project-review.review-component :project_reviewer_id="$project_reviewer_id" />
                            
                        </div>

                        <!-- Footer -->
                        <div class="border-t px-5 py-3 flex justify-end gap-2 bg-slate-50">
                            <button
                                type="button"
                                @click="openReview = false, open=false"
                                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                            >
                                Cancel
                            </button>

                            {{-- <button
                                type="button"
                                @click="openReview=false"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 transition disabled:opacity-50"
                            >
                                <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 018-8" />
                                </svg>
                                Save
                            </button> --}}
                        </div>
                    </div>
                </div>


               
              <!-- ======================== -->
              <!-- ./ Review -->
              <!-- ======================== -->
              @endif



              @if($row['status'] == true && !empty($row['project_reviewer_id']) && $row['review_status'] !== "pending")
              <!-- ======================== -->
              <!-- Submitter response -->
              <!-- ======================== -->
                <button
                    type="button"
                    @click="openRespond = true; 
                      "
                    @if($isFirst  )
                      disabled
                    @endif

                    class="px-2 py-1 text-sm rounded-md bg-blue-500 text-white hover:bg-blue-50 hover:text-blue-500 hover:border-blue-500
                    @if($isFirst  )
                      cursor-not-allowed  pointer-events-none
                        cursor-not-allowed opacity-70 
                    @endif
                    "
                >
                    
                  Respond
                </button>
                <div
                    
                    x-show="openRespond"
                    x-cloak
                    x-transition.opacity
                    @keydown.escape.window="openRespond = false"
                    @click.self="openRespond = false"
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
                        <div class="flex items-center justify-between border-b bg-blue-50 px-5 py-3">
                            <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 4v6h6M20 20v-6h-6M20 4h-6M4 20h6" />
                                </svg>
                                Update submitter project document attachments
                            </h3>
                            <button
                                @click="openRespond = false"
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
                              @php
                              $project_reviewer_id =   $row['project_reviewer_id'];
                              @endphp
                             <livewire:components.project-review.resubmit-component :project_reviewer_id="$project_reviewer_id" />
                            
                        </div>

                        <!-- Footer -->
                        <div class="border-t px-5 py-3 flex justify-end gap-2 bg-slate-50">
                            <button
                                type="button"
                                @click="openRespond = false, open=false"
                                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                            >
                                Cancel
                            </button>

                            {{-- <button
                                type="button"
                                @click="openRespond=false"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 transition disabled:opacity-50"
                            >
                                <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 018-8" />
                                </svg>
                                Save
                            </button> --}}
                        </div>
                    </div>
                </div>


               
              <!-- ======================== -->
              <!-- ./ Submitter response -->
              <!-- ======================== -->
              @endif


              


              {{-- @if($isFirst)
                <!-- disabled -->
                <button
                    type="button"
                    disabled
                    class="px-2 py-1 text-sm  rounded-md
                          bg-sky-100 text-sky-400 
                          border border-sky-200  cursor-not-allowed  pointer-events-none
                          cursor-not-allowed opacity-70 "
                >
                    
                    Edit
                </button>
              @else  --}} 
                <!-- permanently disable the editing of the first reviewer-->

                   
                
                <button
                    type="button"
                    @click="openEdit = true; 
                      "
                    @if($isFirst || $row['review_status'] !== "pending" )
                      disabled
                    @endif
  
                    class="px-2 py-1 text-sm rounded-md bg-sky-50 text-sky-600 hover:bg-sky-100
                    @if($isFirst || $row['review_status'] !== "pending"  )
                      cursor-not-allowed  pointer-events-none
                        cursor-not-allowed opacity-70 
                    @endif
                    "
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
                                Edit Reviewer
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
                              id="{{ $row['row_uid'] }}_period_value"
                              label="Review Period"
                              x-model="updated_period_value"
                              required
                              placeholder="Review Period"
                              {{-- help="Use the official title from the submission." --}}
                              :error="$errors->first('updated_period_value')"
                              type="number"
                              min="0"
                              max="100"
                              displayTooltip
                              position="right"
                              tooltipText="Set the period on how long the review will take"
                              
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



                          @if(!$isFirst)
                            <div class="col-span-6 md:col-span-2" >
                              <x-ui.select
                                id="{{ $row['row_uid'] }}_user_id"
                                label="User"
                                x-model="updated_user_id"
                                placeholder="Select user"
                                :options="$isLast ? $user_admin_options : $user_options"
                                :error="$errors->first('updated_user_id')"
                                displayTooltip="true"
                                position="right"
                                tooltipText="Set the user"
                              />
                            </div>
                          @endif


                          {{-- <div>
                            <x-ui.reviewer.single-select-search

                                x-model="updated_user_id"
                              
                                id="'user_id_'.$row['user_id']"
                                :options="$options" 
                                :selected="$row['user_id']" 
                                label="Select reviewer "
                                class="z-50"
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
                                @click="update('{{ $row['row_uid'] }}',updated_period_value,updated_period_unit,updated_user_id);openEdit=false"
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


              @if($isFirst || $isLast || $row['review_status'] !== "pending" ||  $row['status'] == true)
                

                <button
                  type="button"
                  disabled
                  class="px-2 py-1 text-sm  rounded-md
                        bg-rose-100 text-rose-400 
                        border border-rose-200 cursor-not-allowed  pointer-events-none
                        cursor-not-allowed opacity-70 "
                >
                 

                  Remove
                </button>



              @else 
              

                <button type="button"
                        wire:click="remove('{{ $row['row_uid'] }}', {{ $typeId }})"
                        class="px-2 py-1 text-sm rounded-md bg-rose-50 text-rose-600 hover:bg-rose-100">
                  Remove
                </button>

              @endif

              
 

              @if( !$isFirst && !empty($row['project_reviewer_id'])  )


                @if($row['status'] == false && $row['review_status'] !== "pending" )
                  {{-- -
                  setting reviewer that has already reviewed the project into current reviewer again 
                  REVIEW BACK
                  --}}


                  <button  
                      type="button"
                      onclick="confirm('Are you sure, you want to go back in this review step for this project document?') || event.stopImmediatePropagation()"
                      wire:click.prevent="set_as_current({{ $row['project_reviewer_id'] }})"
                      class="px-2 py-1 text-sm rounded-md bg-slate-500 text-slate-50 hover:bg-slate-800 text-nowrap disabled:opacity-50 disabled:pointer-events-none">
                      Set as Current  
                  </button> 
                @elseif($row['status'] == false && $row['review_status'] == "pending" )

                  {{-- -
                  setting reviewer that has not reviewed the project into current reviewer and skipped previuos reviewers 
                  REVIEW FORWARD
                  --}}

                  <button  
                      type="button"
                      onclick="confirm('Are you sure, you want to go skip other reviewers in this review list and move to this review step for this project document?') || event.stopImmediatePropagation()"
                      wire:click.prevent="set_as_current({{ $row['project_reviewer_id'] }})"
                      class="px-2 py-1 text-sm rounded-md bg-slate-500 text-slate-50 hover:bg-slate-800 text-nowrap disabled:opacity-50 disabled:pointer-events-none">
                      Set as Current  
                  </button> 

                @else

                  <button  
                    type="button"
                    disabled
                    class="px-2 py-1 text-sm rounded-md bg-slate-500 text-slate-50 hover:bg-slate-800 text-nowrap disabled:opacity-50 disabled:pointer-events-none">
                    Set as Current  
                </button> 


                @endif

                 
 
              @else 
              

                 <button  
                    type="button"
                    disabled
                    class="px-2 py-1 text-sm rounded-md bg-slate-500 text-slate-50 hover:bg-slate-800 text-nowrap disabled:opacity-50 disabled:pointer-events-none">
                    Set As Current  
                </button> 

              @endif




              
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-4 py-6 text-sm text-slate-500 text-center">
              No reviewers yet for this document type.
            </td>
          </tr>
        @endforelse
      </tbody>

    </table>
  </div>


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
                    Saving records...
                </div>
            </div>
        </div>

        
    </div> 

  <!--  ./ Loaders -->

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
                    window.pageProjectId = @json(optional(request()->route('project'))->id ?? request()->route('project') ?? null);
                    console.log(window.pageProjectId);

                    const pageProjectId = window.pageProjectId; // can be null
                    // 2) Conditionally listen to the model-scoped user channel
                    if (pageProjectId) {
                        console.log(`listening to : project.project_reviewer.${pageProjectId}`);
                        window.Echo.private(`project.project_reviewer.${pageProjectId}`)
                            .listen('.event', (e) => {
                                console.log('[project model-scoped]');

                                let dispatchEvent = `projectReviewerEvent.${pageProjectId}`;
                                Livewire.dispatch(dispatchEvent); 

                                console.log(dispatchEvent); 

                            });
                    }


                    window.pageProjectDocumentId = @json(optional(request()->route('project_document'))->id ?? request()->route('project_document') ?? null);
                    console.log(window.pageProjectDocumentId);
                    const pageProjectDocumentId = window.pageProjectDocumentId; // can be null
                    // 2) Conditionally listen to the model-scoped user channel
                    if (pageProjectDocumentId) {
                        console.log(`listening to : project.project_document.project_reviewer.${pageProjectDocumentId}`);
                        window.Echo.private(`project.project_document.project_reviewer.${pageProjectDocumentId}`)
                            .listen('.event', (e) => {
                                console.log('[project document model-scoped]');

                                let dispatchEvent = `projectDocumentReviewerEvent.${pageProjectDocumentId}`;
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

<script>
  window.addEventListener('notify', e => console.log(e.detail?.message ?? 'Saved'));
</script>
