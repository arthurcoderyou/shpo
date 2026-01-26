<!-- resources/views/livewire/reviewer-board-per-doc-type.blade.php -->
<!-- Table Section -->
<div class="max-w-full px-4 pb-6 sm:px-6 lg:px-8  mx-auto">

  
    

  <!-- Document Type Selector -->
  <div class="flex gap-3 items-end mb-2">
    <div class="grow">
       

      <x-ui.select
        
        id="currentTypeId"
        name="currentTypeId"
        label="Document type " 

        wire:model.live="currentTypeId"
        :options="$documentTypes"

        displayTooltip
        position="right"
        tooltipText="Select the document type to filter the reviewers list"

      />

    </div>

    <div class="shrink-0">
      {{-- <button type="button"
              wire:click="save"
              class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700">
        Save All
      </button> --}}
      <x-ui.confirm-action-button 
        permission="project reviewer edit"
        button-label="Save All"
        modal-title="Confirm Save"
        modal-message=" Are you sure you want to save these records?"
        wire-action="save"

        displayTooltip 
        position="top"
        tooltipText="Click to Save Reviewer List"
        class="w-full px-2.5 py-2.5 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700"

      />



    </div>
  </div>
 

  
  @php
    $typeId   = (int) $currentTypeId;
    // $options  = $optionsByType[$typeId]  ?? [];
    $selected = $selectedByType[$typeId] ?? [];
    $assigned = $assignedByType[$typeId] ?? [];
  @endphp

  <!-- Multi-select + Times to add -->
 
  <div wire:key="type-{{ $typeId }}" class="grid grid-cols-12 gap-3 items-end mb-2">


     {{-- 


    <div 
      class="col-span-12 md:col-span-4" 
    
    >
      <label class="inline-block text-sm font-medium text-gray-800">
        Select reviewers ({{ $documentTypes[array_search($typeId, array_column($documentTypes,'id'))]['name'] ?? '' }})
      </label>

      <div
        x-data="{
          open:false,   // dropdown open/closed state
          search:'',    // search filter
          options:@js($options),                               // [{id,name,roles:[]}]
          selected:@entangle('selectedByType.' . $typeId),          //  selected array of options [users] as a reviewer to a document type 
          roleColors: {       // role colors 
            'global admin': 'bg-red-100 text-red-700 ring-red-200',
            'admin':        'bg-amber-100 text-amber-800 ring-amber-200',
            'reviewer':     'bg-sky-100 text-sky-700 ring-sky-200',
            'user':         'bg-slate-100 text-slate-700 ring-slate-200',
            '__none':       'bg-zinc-100 text-zinc-700 ring-zinc-200', // no role
          },
          toggle(id){     // toggle remove / add options [users] to the selected multi-select input 
            this.isSelected(id) ? this.remove(id) : this.add(id)    // check if it is selected first, then common sense that if it is not, then the id is to be added but if it is already included then it is to be removed  
          },
          add(id){    // adds the id to the selected array list 
            if(!this.isSelected(id)) this.selected.push(id) 
          },
          remove(id){   // removes the id on the selected array list 
            this.selected = (this.selected ?? []).filter(v => v !== id) 
          },
          isSelected(id){   // function to check if the id is selected 
            return (this.selected ?? []).includes(id) 
          }, 
          labelFor(id){      // Looks up the option by id. Returns its name if found, otherwise just the raw id.
            const o = this.options.find( o => o.id === id); 
            return o?o.name:id; 
          },
          rolesFor(id){     // Gets the array of roles for the given user.  Returns empty array if not found.
            const o = this.options.find( o => o.id === id); 
            return o ? (o.roles ?? []) : []; 
          },
          badgeCls(role){  
            const key = (role || '').toLowerCase();   // Normalizes the role name to lowercase. 
            const base = 'px-1.5 py-0.5 rounded-md text-xs ring-1';   // always added 
            return `${base} ${(this.roleColors[key] ?? this.roleColors['__none'])}`;   //  Returns a base set of Tailwind classes plus a role-specific color. If no matching role, falls back to __none.
          },
          filterList(){
            const q = this.search.trim().toLowerCase();   // Reads search input, lowercases it.
            if(!q) return this.options;     // If empty, returns all options.
            return this.options.filter(o => {       // Otherwise filters options:
              const inName = (o.name || '').toLowerCase().includes(q);    // Matches if the name contains the search string, OR
              const inRoles = (o.roles || []).some(r => (r||'').toLowerCase().includes(q));     // Any of the user’s roles contain the search string.
              return inName || inRoles;
            });
          }
        }"
        class="relative"
      >
        <!-- Trigger -->
        <div
          @click="open = !open"
          class="border rounded-lg px-3 py-1 flex flex-wrap items-center gap-2 bg-white focus-within:ring-2 focus-within:ring-sky-500 min-h-[44px]"
        >
          <!-- Selected chips -->
          <template x-for="id in (selected ?? [])" :key="id">
            <span class="bg-indigo-100 text-indigo-700 text-sm px-2 py-1 round` ed-full flex items-center gap-2">
              <span class="flex items-center gap-2">

                <span x-text="labelFor(id)"></span>   <!-- displays the opion [user] name or id if null -->

                <!-- role badges inside chip -->
                <template x-for="role in rolesFor(id)" :key="role">
                  <span :class="badgeCls(role)" x-text="role"></span>
                </template>
                
                
                <!-- no role -->
                <template x-if="rolesFor(id).length === 0">
                  <span :class="badgeCls('')" >No role</span>
                </template>


              </span>

              <button type="button" @click.stop="remove(id)" class="leading-none">&times;</button>  <!-- remove the id from the selected options [users]-->
            </span>
          </template>

          <!-- search input -->
          <input
            type="text"
            x-model="search"
            placeholder="Search by name or role…"
            class="flex-grow border-0 focus:ring-0 text-sm text-gray-700 outline-none"
          />


        </div>

        <!-- Dropdown -->
         
        <div
          x-show="open" x-transition @click.outside="open=false" x-cloak
          class="absolute left-0 right-0 z-50 mt-1 bg-white border rounded-lg shadow max-h-60 overflow-y-auto"
        >
          <template x-for="opt in filterList()" :key="opt.id">
            <button
              type="button"
              @click="toggle(opt.id)"
              class="w-full text-left px-3 py-2 flex items-center justify-between hover:bg-indigo-50"
            >
              <span class="flex flex-col">
                <span class="font-medium" x-text="opt.name"></span>
                <span class="mt-1 flex flex-wrap gap-1.5">
                  <!-- role badges -->
                  <template x-for="role in (opt.roles ?? [])" :key="role">
                    <span :class="badgeCls(role)" x-text="role"></span>
                  </template>
                  <!-- no role -->
                  <template x-if="(opt.roles ?? []).length === 0">
                    <span :class="badgeCls('')">No role</span>
                  </template>
                </span>
              </span>

              <span x-show="isSelected(opt.id)" class="text-indigo-600 font-bold">✓</span>
            </button>
          </template>

          <div
            x-show="filterList().length===0"
            class="px-3 py-2 text-sm text-slate-500"
          >
            No results
          </div>
        </div>


      </div>
    </div>
    --}}

    {{--  
    <!-- Times to add -->
    <div class="col-span-6 md:col-span-2">
      <label class="block text-sm font-medium text-gray-800 ">Times to add</label>
      <input type="number" min="1" max="20"
            wire:model.lazy="repeatByType.{{ $typeId }}"
            class="w-full py-2.5 px-3 rounded-lg border border-gray-300 text-sm focus:ring-sky-500 focus:border-sky-500"
            placeholder="1">
    </div>
    --}}

    @if(count($assignedByType[$currentTypeId]) >= 1)

    <!-- Multi-select + Times to add --> 
    <div class="col-span-12 md:col-span-8">
        <x-ui.reviewer.multi-select-search
            :id="'selectedByType.' . $typeId"
            :options="$options"
            :entangle="'selectedByType.' . $typeId"
            label="Select reviewers  "
        />
    </div> 

 


    <div class="col-span-6 md:col-span-2" >
      {{-- <label class="block text-sm font-medium text-gray-800 ">Times to add</label>
      <input type="number" min="1" max="20"
            wire:model.lazy="repeatByType.{{ $typeId }}"
            class="w-full py-2.5 px-3 rounded-lg border border-gray-300 text-sm focus:ring-sky-500 focus:border-sky-500"
            placeholder="1"> --}}


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


    {{-- <div class="col-span-6 md:col-span-2" >
      <x-ui.select
        id="period_unit"
        label="Period Unit"
        wire:model.live="period_unit"
        placeholder="Select a unit"
        :options="$period_unit_options"
        :error="$errors->first('period_unit')"
        displayTooltip="true"
        position="right"
        tooltipText="Set the review period unit (day, week, month)"
      />
    </div> --}}


    <div class="col-span-6 md:col-span-2 flex gap-2">
      <!-- Add selected to the main assigned reviewers list -->
      <button type="button" wire:click="addSelected"
              class="w-full text-nowrap py-2.5 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg bg-sky-600 text-white hover:bg-sky-700">
        Add to Table
      </button>

      <x-ui.table.reset-button wireClick="resetFilters" />
      
    </div>
    @endif


    {{-- -
    <div class="col-span-6 md:col-span-2">
      <!-- Add selected to the main assigned reviewers list -->
      <button type="button" wire:click="addOpenSlots('admin')"
              class="w-full text-nowrap py-2.5 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
        Add Admin Review
      </button>
    </div>
     --}}

    
  </div>


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
          <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Review Period</th>
          <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600">Roles</th>
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

              }

            }"
 

            data-uid="{{ $row['row_uid'] }}"
            {{-- Only make middle rows draggable --}}
            @unless($isFirst || $isLast)
                draggable="true"
                @dragstart="start($event, '{{ $row['row_uid'] }}')"
                @dragover="over($event)"
                @drop="drop($event, '{{ $row['row_uid'] }}')"
            @endunless
            class="bg-white hover:bg-slate-50"
            
          >
            <td class="px-4 py-2">
              <div class="flex items-center gap-2"> 

                @if($isFirst || $isLast)
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
                  {{-- <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-medium bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">
                    Current reviewer
                  </span> --}}
                </div>
              @else
                <div class="flex items-center gap-2">
                  <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium
                      {{ ($row['slot_role'] ?? 'reviewer') === 'admin' ? 'bg-amber-100 text-amber-800' : 'bg-indigo-100 text-indigo-800' }}">
                    {{-- Open • {{ ucfirst($row['slot_role']) }} --}}
                    Open Admin Review
                  </span>

                  {{-- @if(!empty($row['user_id']) && !empty($row['name']))
                    <span class="text-xs text-slate-500">claimed by</span>
                    <span class="text-sm text-slate-800">{{ $row['name'] }}</span>
                  @else
                    <span class="text-xs text-slate-500">not claimed</span>
                  @endif --}}
                </div>
              @endif
                


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

            <td class="px-4 py-2 text-right flex space-x-2"
            >

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


              @if($isFirst || $isLast)
                

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

</div>

<script>
  window.addEventListener('notify', e => console.log(e.detail?.message ?? 'Saved'));
</script>
