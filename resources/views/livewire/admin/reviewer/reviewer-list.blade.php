<!-- resources/views/livewire/reviewer-board-per-doc-type.blade.php -->
<!-- Table Section -->
<div class="max-w-[85rem] px-4 py-6 sm:px-6 lg:px-8  mx-auto">

  
    

  <!-- Document Type Selector -->
  <div class="flex gap-3 items-end mb-2">
    <div class="grow">
      <label class="block text-sm font-medium text-gray-800 ">Document Type</label>
      <select
        wire:model.live="currentTypeId"
        class="w-full py-2.5 px-3 rounded-lg border border-gray-300 text-sm focus:ring-sky-500 focus:border-sky-500"
      >
        @foreach($documentTypes as $t)
          <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
        @endforeach
      </select>
    </div>

    <div class="shrink-0">
      <button type="button"
              wire:click="save"
              class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700">
        Save All
      </button>
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
    <div 
      class="col-span-12 md:col-span-8" 
    
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
        {{-- 
        x-show="open"
         --}} 
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


    {{-- <!-- Times to add -->
    <div class="col-span-6 md:col-span-2">
      <label class="block text-sm font-medium text-gray-800 ">Times to add</label>
      <input type="number" min="1" max="20"
            wire:model.lazy="repeatByType.{{ $typeId }}"
            class="w-full py-2.5 px-3 rounded-lg border border-gray-300 text-sm focus:ring-sky-500 focus:border-sky-500"
            placeholder="1">
    </div> --}}

    <div class="col-span-6 md:col-span-2">
      <!-- Add selected to the main assigned reviewers list -->
      <button type="button" wire:click="addSelected"
              class="w-full py-2.5 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg bg-sky-600 text-white hover:bg-sky-700">
        Add to Table
      </button>
    </div>

    <div class="col-span-6 md:col-span-2">
      <!-- Add selected to the main assigned reviewers list -->
      <button type="button" wire:click="addOpenSlots('admin')"
              class="w-full py-2.5 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
        Add Admin Review
      </button>
    </div>

    
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
          <tr
            data-row 
            x-data="{
              {{-- roles:@js($row['roles'] ?? []), --}}
              {{-- userId:@js($row['user_id'] ?? null), --}}
            }"

            data-uid="{{ $row['row_uid'] }}"
            
            draggable="true"
            @dragstart="start($event, '{{ $row['row_uid'] }}')"
            @dragover="over($event)"
            @drop="drop($event, '{{ $row['row_uid'] }}')"
            class="bg-white hover:bg-slate-50"
            
          >
            <td class="px-4 py-2">
              <div class="flex items-center gap-2"> 
                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 9h2v2H7V9zm4 0h2v2h-2V9zM7 14h2v2H7v-2zm4 0h2v2h-2v-2z"/></svg>
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

            <td class="px-4 py-2 text-right">
              <button type="button"
                      wire:click="remove('{{ $row['row_uid'] }}', {{ $typeId }})"
                      class="px-2 py-1 text-sm rounded-md bg-rose-50 text-rose-600 hover:bg-rose-100">
                Remove
              </button>
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




</div>

<script>
  window.addEventListener('notify', e => console.log(e.detail?.message ?? 'Saved'));
</script>
