<!-- resources/views/livewire/reviewer-board-per-doc-type.blade.php -->
<div class="space-y-6">

  <!-- Document Type Selector -->
  <div class="flex gap-3 items-end">
    <div class="grow">
      <label class="block text-sm font-medium text-gray-800 mb-2">Document Type</label>
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
  $options  = $optionsByType[$typeId]  ?? [];
  $selected = $selectedByType[$typeId] ?? [];
  $assigned = $assignedByType[$typeId] ?? [];
@endphp

<!-- 🔑 Everything below this line is re-rendered when type changes -->
<div wire:key="type-{{ $typeId }}" class="space-y-4">
  <!-- Multi-select (disabled if already assigned) -->
  <div
    x-data="{
      open:false, search:'',
      options:@js($options),
      assignedIds:@js(array_map('intval', array_column($assigned,'id'))),  // <-- for disabling
      selected:@entangle('selectedByType.' . $typeId),
      toggle(id){ this.isSelected(id) ? this.remove(id) : this.add(id) },
      add(id){ if(!this.isSelected(id) && !this.assignedIds.includes(id)) this.selected.push(id) },
      remove(id){ this.selected = this.selected.filter(v => v !== id) },
      isSelected(id){ return (this.selected ?? []).includes(id) },
      labelFor(id){ const o=this.options.find(o=>o.id===id); return o?o.name:id; }
    }"
    class="relative"
  >
    <div @click="open=!open"
         class="border rounded-lg px-3 py-2 flex flex-wrap items-center gap-2 bg-white focus-within:ring-2 focus-within:ring-sky-500">
      <template x-for="id in selected" :key="id">
        <span class="bg-indigo-100 text-indigo-700 text-sm px-2 py-1 rounded-full flex items-center gap-1">
          <span x-text="labelFor(id)"></span>
          <button type="button" @click.stop="remove(id)" class="leading-none">&times;</button>
        </span>
      </template>
      <input type="text" x-model="search" placeholder="Search reviewers…"
             class="flex-grow border-0 focus:ring-0 text-sm text-gray-700 outline-none" />
    </div>

    <div x-show="open" x-transition @click.outside="open=false"
         class="absolute left-0 right-0 z-50 mt-1 bg-white border rounded-lg shadow max-h-60 overflow-y-auto" x-cloak>
      <template x-for="opt in options.filter(o => o.name.toLowerCase().includes(search.toLowerCase()))" :key="opt.id">
        <button type="button"
                @click="toggle(opt.id)"
                class="w-full text-left px-3 py-2 flex items-center justify-between hover:bg-indigo-50"
                :class="assignedIds.includes(opt.id) ? 'opacity-50 cursor-not-allowed' : ''"
                :disabled="assignedIds.includes(opt.id)">
          <span x-text="opt.name"></span>
          <template x-if="assignedIds.includes(opt.id)">
            <span class="text-slate-400 text-xs ml-2">(already in table)</span>
          </template>
          <span x-show="isSelected(opt.id)" class="text-indigo-600 font-bold">✓</span>
        </button>
      </template>

      <div x-show="options.filter(o => o.name.toLowerCase().includes(search.toLowerCase())).length===0"
           class="px-3 py-2 text-sm text-slate-500">No results</div>
    </div>
  </div>

  <div class="flex justify-end">
    <button type="button" wire:click="addSelected"
            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-sky-600 text-white hover:bg-sky-700">
      Add to Table
    </button>
  </div>

  <!-- Draggable table (also keyed by type) -->
  <div
    x-data="{
      draggingId: null,
      start(e,id){ this.draggingId = id; e.dataTransfer.effectAllowed='move' },
      over(e){ e.preventDefault(); e.dataTransfer.dropEffect='move' },
      drop(e, targetId){
        e.preventDefault();
        if(this.draggingId===null || this.draggingId===targetId) return;
        const rows = Array.from($el.querySelectorAll('[data-row]')).map(r => Number(r.dataset.id));
        const from = rows.indexOf(this.draggingId);
        const to   = rows.indexOf(targetId);
        rows.splice(to, 0, rows.splice(from,1)[0]);
        @this.reorder({{ $typeId }}, rows);
        this.draggingId = null;
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
          <th class="w-24 px-4 py-2"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200">
        @forelse($assigned as $row)
          <tr data-row :data-id="{{ (int) $row['id'] }}"
              draggable="true"
              @dragstart="start($event, {{ (int) $row['id'] }})"
              @dragover="over($event)"
              @drop="drop($event, {{ (int) $row['id'] }})"
              class="bg-white hover:bg-slate-50">
            <td class="px-4 py-2">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor"><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 9h2v2H7V9zm4 0h2v2h-2V9zM7 14h2v2H7v-2zm4 0h2v2h-2v-2z"/></svg>
                <span class="text-sm text-slate-700">#{{ $row['order'] }}</span>
              </div>
            </td>
            <td class="px-4 py-2">
              <span class="text-sm text-slate-800">{{ $row['name'] }}</span>
            </td>
            <td class="px-4 py-2 text-right">
              <button type="button" wire:click="remove({{ (int) $row['id'] }}, {{ $typeId }})"
                      class="px-2 py-1 text-sm rounded-md bg-rose-50 text-rose-600 hover:bg-rose-100">
                Remove
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="px-4 py-6 text-sm text-slate-500 text-center">
              No reviewers yet for this document type.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>




</div>

<script>
  window.addEventListener('notify', e => console.log(e.detail?.message ?? 'Saved'));
</script>
