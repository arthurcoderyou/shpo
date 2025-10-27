@props(['status' => 'pending'])

@php
    $map = [
        'approved'           => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'changes-requested'  => 'bg-amber-50 text-amber-700 ring-amber-200',
        'in-review'          => 'bg-sky-50 text-sky-700 ring-sky-200',
        'draft'              => 'bg-slate-100 text-slate-700 ring-slate-200',
    ];

    $label = match($status) {
        'approved' => 'Approved',
        'changes-requested' => 'Changes requested',
        'in-review' => 'In review',
        'draft' => 'Draft',
        default => ucfirst($status),
    };
@endphp

<span class="rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 ring-inset {{ $map[$status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
    {{ $label }}
</span>
