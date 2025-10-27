<tr class="group bg-white hover:bg-slate-50" wire:key="project-{{ $row->id }}">
  <td class="px-3 py-1.5 {{ ($stickyFirstCol ?? false) ? 'sticky left-0 z-20 bg-white/95 backdrop-blur border-r border-slate-200 overflow-visible' : '' }}">
    <button wire:click="open({{ $row->id }})" class="rounded-md bg-black px-2.5 py-1 text-xs font-medium text-white">Show</button>
  </td>

  <td class="px-4 py-2">
    <a href="#" class="font-medium text-slate-900 hover:underline">{{ $row->name }}</a>
    <div class="text-xs text-slate-500">{{ $row->type ?? '—' }}</div>
  </td>

  <td class="px-4 py-2 text-slate-700">{{ $row->submitter?->name ?? '—' }}</td>

  <td class="px-4 py-2">
    <div class="min-w-0">
      <a href="#" class="truncate font-medium text-slate-900 hover:underline">{{ $row->currentDocument?->name ?? '—' }}</a>
      <div class="text-xs text-slate-500">
        {{ $row->currentDocument?->mime ?? '' }}
        @if($row->currentDocument?->size_human) • {{ $row->currentDocument->size_human }} @endif
      </div>
    </div>
  </td>

  <td class="px-4 py-2">
    @include('livewire.partials.status-badge', ['status' => $row->review_status])
  </td>

  <td class="px-4 py-2 text-slate-600">{{ optional($row->submitted_at)->format('M d, Y') ?? '—' }}</td>
  <td class="px-4 py-2 text-slate-600">{{ optional($row->updated_at)->diffForHumans() ?? '—' }}</td>

  <td class="px-4 py-2 text-slate-700">
    {{-- your recent docs UI --}}
  </td>
</tr>
