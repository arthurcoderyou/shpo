<?php

use Livewire\Volt\Component;

new class extends Component {
    // ✅ Only arrays / scalars (Livewire-friendly)
    public array $rows = [];

    public string $headView = '';
    public string $rowView  = '';

    // Optional UI knobs
    public bool $stickyFirstCol = false;
    public string $tableClass   = 'min-w-full';
    public string $wrapperClass = 'overflow-auto rounded-2xl border border-slate-200 bg-white shadow-sm';

    // Pagination meta (optional)
    // Example: ['first'=>1,'last'=>15,'total'=>120,'links'=>'<nav>...</nav>']
    public ?array $page = null;

    // Empty state
    public ?string $emptyView = null;
    public string $emptyText  = 'No records found.';
};
?>

<div class="{{ $wrapperClass }}">
  <table class="{{ $tableClass }}">
    <thead class="bg-slate-50">
      @include($headView, ['stickyFirstCol' => $stickyFirstCol])
    </thead>

    <tbody class="divide-y divide-slate-200 text-sm">
      @php $hasAny = !empty($rows); @endphp

      @foreach ($rows as $row)
        @include($rowView, ['row' => $row, 'stickyFirstCol' => $stickyFirstCol])
      @endforeach

      @unless($hasAny)
        @if ($emptyView)
          @include($emptyView)
        @else
          <tr>
            <td colspan="100" class="px-4 py-6 text-center text-slate-500">{{ $emptyText }}</td>
          </tr>
        @endif
      @endunless
    </tbody>
  </table>

  {{-- Pagination footer (if provided) --}}
  @if ($page)
    <div class="flex items-center justify-between px-4 py-3">
      <div class="text-xs text-slate-500">
        Showing
        <span class="font-medium">{{ $page['first'] ?? 0 }}</span>
        to
        <span class="font-medium">{{ $page['last'] ?? 0 }}</span>
        of
        <span class="font-medium">{{ $page['total'] ?? 0 }}</span>
        results
      </div>
      <div class="text-sm">
        {!! $page['links'] ?? '' !!}
      </div>
    </div>
  @endif
</div>
