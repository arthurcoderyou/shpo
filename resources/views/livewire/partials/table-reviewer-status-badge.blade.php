<?php

use Livewire\Volt\Component;
use Carbon\Carbon;

new class extends Component {
    /** Passed in props */
    public string $status;
    /** @var \App\Models\ProjectDocument|\Illuminate\Database\Eloquent\Model|mixed */
    public $projectDocument;

    public function with()
    {
        // 1) Status badge styles
        $map = [
            'draft' => ['label' => 'Draft', 'bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'ring' => 'ring-slate-200'],
            'submitted' => ['label' => 'Submitted', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'ring' => 'ring-blue-200'],
            'in_review' => ['label' => 'In Review', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'ring' => 'ring-amber-200'],
            'approved' => ['label' => 'Approved', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-200'],
            'rejected' => ['label' => 'Rejected', 'bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'ring' => 'ring-rose-200'],
            'completed' => ['label' => 'Completed', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'ring' => 'ring-indigo-200'],
            'cancelled' => ['label' => 'Cancelled', 'bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'ring' => 'ring-gray-200'],
        ];

        $config = $map[$this->status] ?? [
            'label' => ucfirst(str_replace('_', ' ', (string) $this->status)),
            'bg' => 'bg-slate-100',
            'text' => 'text-slate-500',
            'ring' => 'ring-slate-200',
        ];

        // 2) Current reviewer (active/true, by order asc)
        $reviewer = null;
        if ($this->projectDocument && method_exists($this->projectDocument, 'getCurrentReviewerByProjectDocument')) {
            $reviewer = $this->projectDocument->getCurrentReviewerByProjectDocument();
        }

        // 3) Slot details
        $slotType = $reviewer->slot_type ?? null; // 'open' | 'person'
        $slotRole = $reviewer->slot_role ?? null; // only shown for 'person'

        // 4) Reviewer display name
        $reviewerName = 'Open review';
        if ($slotType === 'person') {
            // assumes relation $reviewer->user
            $reviewerName = optional($reviewer->user)->name ?: 'Unassigned person';
        }else{
            if(!empty($reviewer->user_id)){
                // assumes relation $reviewer->user
                $reviewerName = optional($reviewer->user)->name ?: 'Unassigned person';
            }

        }

        // 5) Reviewer status on the reviewer instance (e.g., 'pending','accepted','returned', etc.)
        $reviewStatus = $reviewer->review_status ?? null;

        // 6) Expected due date
        // Prefer explicit due date on Project Document; fallback to timer_count + timer_type
        $dueAt = null;
        if (!empty($this->projectDocument->reviewer_due_date)) {
            $dueAt = Carbon::parse($this->projectDocument->reviewer_due_date);
        } else {
            $count = (int) ($this->projectDocument->reviewer_response_timer_count ?? 0);
            $type  = (string) ($this->projectDocument->reviewer_response_timer_type ?? '');
            if ($count > 0 && $type) {
                // normalize type
                switch (strtolower($type)) {
                    case 'day':
                    case 'days':
                        $dueAt = Carbon::now()->addDays($count);
                        break;
                    case 'week':
                    case 'weeks':
                        $dueAt = Carbon::now()->addWeeks($count);
                        break;
                    case 'month':
                    case 'months':
                        $dueAt = Carbon::now()->addMonths($count);
                        break;
                    case 'year':
                    case 'years':
                        $dueAt = Carbon::now()->addYears($count);
                        break;
                }
            }
        }

        $dueAtText = $dueAt ? $dueAt->timezone(config('app.timezone', 'UTC'))->format('M d, Y g:ia') : null;
        $dueAtDiff = $dueAt ? $dueAt->diffForHumans() : null;

        // 7) Flags (booleans) stored on the reviewer instance
        $flags = [
            'requires_project_update'   => (bool) ($reviewer->requires_project_update ?? false),
            'requires_document_update'  => (bool) ($reviewer->requires_document_update ?? false),
            'requires_attachment_update'=> (bool) ($reviewer->requires_attachment_update ?? false),
        ];

        return compact('config', 'reviewerName', 'reviewStatus', 'slotType', 'slotRole', 'dueAtText', 'dueAtDiff', 'flags');
    }
};
?>

<td class="px-4 py-2 align-top">
    <!-- Status badge -->
    <div class="mb-1">
        <span class="inline-flex items-center gap-1 rounded-full {{ $config['bg'] }} px-2 py-0.5 text-[11px] font-semibold {{ $config['text'] }} ring-1 ring-inset {{ $config['ring'] }}">
            {{ $config['label'] }}
        </span>
    </div>

    <!-- Reviewer block -->
    <div class="space-y-1 text-[11px] leading-4 text-slate-600">
        <div class="flex items-center gap-1">
            <!-- Eye/user icon -->
            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            <span class="font-medium text-slate-700">Reviewer:</span>
            <span class="truncate">{{ $reviewerName }}</span>

            @if($slotType === 'person' && !empty($slotRole))
                <span class="ml-1 rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] text-slate-700">{{ $slotRole }}</span>
            @else   
                @if($slotType === 'open' && !empty($projectDocument->user_id))
                    <span class="ml-1 rounded border border-sky-200 bg-sky-50 px-1.5 py-0.5 text-[10px] text-sky-700">Claimed</span>
                @else
                    <span class="ml-1 rounded border border-amber-200 bg-amber-50 px-1.5 py-0.5 text-[10px] text-amber-700">Open</span>
                @endif
            @endif
        </div>

        @if(!empty($reviewStatus))
        <div class="flex items-center gap-1">
            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414L9 13.414l4.707-4.707Z"/>
            </svg>
            <span class="font-medium text-slate-700">Review status:</span>
            <span class="uppercase tracking-wide">{{ $reviewStatus }}</span>
        </div>
        @endif

        <div class="flex items-center gap-1">
            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3.5 9h17M5 20h14a2 2 0 0 0 2-2v-9H3v9a2 2 0 0 0 2 2Z"/>
            </svg>
            <span class="font-medium text-slate-700">Expected:</span>
            @if($dueAtText)
                <span>{{ $dueAtText }}</span>
                <span class="text-slate-400">({{ $dueAtDiff }})</span>
            @else
                <span class="italic text-slate-400">No due date</span>
            @endif
        </div>

        <!-- Flags -->
        <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
            @php
                $flagLabels = [
                    'requires_project_update' => 'Project update',
                    'requires_document_update' => 'Document update',
                    'requires_attachment_update' => 'Attachment update',
                ];
            @endphp

            @foreach($flagLabels as $key => $label)
                @if($flags[$key] ?? false)
                    <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-1.5 py-0.5 ring-1 ring-amber-200 text-[10px] font-medium text-amber-700">
                        <svg class="size-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a8 8 0 100 16 8 8 0 000-16Zm.75 4a.75.75 0 00-1.5 0v5.25c0 .414.336.75.75.75h3.5a.75.75 0 000-1.5h-2.75V6Z"/></svg>
                        {{ $label }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-50 px-1.5 py-0.5 ring-1 ring-slate-200 text-[10px] text-slate-500">
                        <svg class="size-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-1-5 5-5-1.414-1.414L9 10.172 7.414 8.586 6 10l3 3Z"/></svg>
                        {{ $label }}
                    </span>
                @endif
            @endforeach
        </div>
    </div>
</td>
