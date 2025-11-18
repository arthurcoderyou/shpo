{{-- resources/views/components/review/previous-reviewer.blade.php --}}
@props([
    // The review model/array for the previous review
    'review' => null,

    // Optional Livewire actions (method names) for the quick-action buttons
    'onViewProfile' => null,          // e.g. 'viewReviewerProfile'
    'onOpenHistory' => null,          // e.g. 'openReviewHistory'
    'onCompareIteration' => null,     // e.g. 'compareWithPreviousIteration'

    // UI toggles
    'showActions' => true,
    'defaultOpenNotes' => true,
])

@php
    $rev = $review;

    $reviewerId    = $rev->created_by ?? $rev['created_by'] ?? null;


    $user = auth()->user();

    if ($user) {
        // Name & initial from the authenticated user 

        // Build subtitle from Spatie permissions
        // Map: permission => label
        $permMap = [
            'system access global admin' => 'Global Administrator',
            'system access admin'        => 'Admin',
            'system access reviewer'     => 'Reviewer',
            'system access user'         => 'Submitter',
        ];

        $labels = [];
        foreach ($permMap as $perm => $label) {
            // Works with Spatie via Gate: $user->can('permission-name')
            if ($user->can($perm)) {
                $labels[] = $label;
            }
        }

        $roleLabel = count($labels) ? implode(' / ', $labels) : 'Guest';
    }

    $reviewerName = optional($rev->creator ?? null)->name
        ?? ($rev['creator']['name'] ?? null);
    $reviewerEmail = optional($rev->creator ?? null)->email
                    ?? ($rev['creator']['email'] ?? null);

    $iteration     = $rev->iteration ?? $rev['iteration'] ?? 1;
    
    $statusRaw     = $rev->review_status ?? $rev['review_status'] ?? 'PENDING';
    $status        = strtoupper($statusRaw);

    $reviewedAtRaw = $rev->reviewed_at ?? $rev['reviewed_at'] ?? ($rev->created_at ?? $rev['created_at'] ?? null);
    try {
        $reviewedAt = $reviewedAtRaw ? \Illuminate\Support\Carbon::parse($reviewedAtRaw)->format('M d, Y h:i a') : null;
    } catch (\Throwable $e) {
        $reviewedAt = null;
    }

    $docType   = $rev->document_type ?? $rev['document_type'] ?? '—';
    $score     = $rev->score ?? $rev['score'] ?? '—';
    $notes     = $rev->project_review ?? $rev['project_review'] ?? 'No notes provided';
    $files     = $rev->attachments ?? $rev['attachments'] ?? [];

    $statusMap = [
        'APPROVED'   => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-100'],
        'REVIEWED'   => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'ring' => 'ring-green-100'],
        'REJECTED'   => ['bg' => 'bg-rose-50',    'text' => 'text-rose-700',    'ring' => 'ring-rose-100'],
        'CHANGES_REQUESTED'    => ['bg' => 'bg-amber-50',   'text' => 'text-amber-800',   'ring' => 'ring-amber-100'],
        'PENDING'    => ['bg' => 'bg-slate-50',   'text' => 'text-slate-700',   'ring' => 'ring-slate-100'],
        'IN_REVIEW'  => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-700',  'ring' => 'ring-indigo-100'],
    ];
    $c = $statusMap[$status] ?? $statusMap['PENDING'];

    // Avatar initials
    $initials = collect(preg_split('/\s+/', trim($reviewerName), -1, PREG_SPLIT_NO_EMPTY))
        ->map(fn($p) => mb_substr($p, 0, 1))
        ->take(2)->implode('');

    // Build action attributes if provided
    $attrViewProfile   = $onViewProfile   ? "wire:click=\"{$onViewProfile}({$reviewerId})\"" : '';
    $attrOpenHistory   = $onOpenHistory   ? "wire:click=\"{$onOpenHistory}({$reviewerId})\"" : '';
    $attrCompare       = $onCompareIteration ? "wire:click=\"{$onCompareIteration}({$iteration})\"" : '';
@endphp

<div class="rounded-2xl border bg-white shadow-sm overflow-hidden">
    <!-- Top: identity + status -->
    <div class="px-5 py-4 border-b bg-gradient-to-r from-amber-50 via-white to-white">
        <div class="flex items-start gap-4">

            <!-- Avatar -->
            <div class="relative shrink-0">
                <div class="h-12 w-12 rounded-full bg-amber-600 text-white grid place-items-center font-semibold">
                    {{ $initials }}
                </div>
                <span class="absolute -bottom-1 -right-1 inline-flex items-center justify-center h-5 w-5 rounded-full bg-white ring-2 ring-amber-100">
                    <!-- briefcase icon -->
                    <svg class="h-3.5 w-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7V6a2 2 0 012-2h2a2 2 0 012 2v1M3 13a4 4 0 014-4h10a4 4 0 014 4v3a3 3 0 01-3 3H6a3 3 0 01-3-3v-3z"/>
                    </svg>
                </span>
            </div>

            <!-- Name + meta -->
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h4 class="text-base sm:text-lg font-semibold text-slate-900 truncate">
                        {{ $reviewerName }}
                    </h4>

                    <!-- Role pill -->
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium px-2.5 py-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6l9 4.5-9 4.5L3 10.5 12 6z"/>
                        </svg>
                        {{ $roleLabel }}
                    </span>

                    {{-- <!-- Iteration pill -->
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 text-amber-900 text-xs font-semibold px-2.5 py-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v6h6M20 20v-6h-6"/>
                        </svg>
                        Iteration {{ $iteration }}
                    </span> --}}

                    <!-- Status chip -->
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $c['bg'] }} {{ $c['text'] }} ring-1 {{ $c['ring'] }}">
                        {{ $status }}
                    </span>
                </div>

                <!-- Email + reviewed time -->
                <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600">
                    @if($reviewerEmail)
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 18h14"/>
                            </svg>
                            <a class="underline hover:text-slate-800" href="mailto:{{ $reviewerEmail }}">{{ $reviewerEmail }}</a>
                        </span>
                    @endif
                    @if($reviewedAt)
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 11h16M7 15h5"/>
                            </svg>
                            Reviewed: <span class="ml-1 font-medium text-slate-800">{{ $reviewedAt }}</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Quick actions -->
            @if($showActions)
                <div class="flex flex-col items-end gap-2">
                    <div class="inline-flex rounded-lg border border-slate-200 overflow-hidden">
                        <button type="button" class="px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50"
                            @if($attrViewProfile) {!! $attrViewProfile !!} @endif>
                            View Profile
                        </button>
                        <button type="button" class="px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50 border-l border-slate-200"
                            @if($attrOpenHistory) {!! $attrOpenHistory !!} @endif>
                            History
                        </button>
                    </div>

                    <button type="button" class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 hover:text-amber-800"
                        @if($attrCompare) {!! $attrCompare !!} @endif>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Compare changes
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Bottom: summary + notes (collapsible) -->
    <div x-data="{ openNotes: @js($defaultOpenNotes) }" class="px-5 py-4">
        <div class="flex items-center justify-between">
            <p class="font-semibold text-slate-800">Previous Review Summary</p>
            <button
                type="button"
                @click="openNotes = !openNotes"
                class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-slate-800"
            >
                <span x-text="openNotes ? 'Hide details' : 'Show details'"></span>
                <svg class="w-4 h-4 transition"
                     :class="openNotes ? 'rotate-180' : ''"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <div x-show="openNotes" x-collapse class="mt-3 space-y-3">
            <!-- quick metadata badges -->

            {{-- <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 text-slate-700 px-2 py-0.5 text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16"/>
                    </svg>
                    Doc: {{ $docType }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 text-slate-700 px-2 py-0.5 text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5v14"/>
                    </svg>
                    Score: {{ $score }}
                </span>
                <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 text-slate-700 px-2 py-0.5 text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8"/>
                    </svg>
                    Iteration {{ $iteration }}
                </span>
            </div> --}}

            <!-- notes -->
            <div>
                <p class="text-xs text-slate-500 mb-1">Notes</p>
                <div class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-sm text-slate-800">
                    {{ $notes }}
                </div>
            </div>

            @if(!empty($files) && count($files) > 0)
                <div>
                    <p class="text-xs text-slate-500 mb-1">Attachments</p>
                    <ul class="space-y-1 text-sm">
                        @foreach($files as $file)
                            @php
                                $fUrl = is_array($file) ? ($file['url'] ?? '#') : (is_string($file) ? $file : '#');
                                $fName = is_array($file) ? ($file['name'] ?? basename($fUrl)) : (is_string($file) ? basename($file) : 'Attachment');
                            @endphp
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16h8M8 12h8M12 8h8M4 12l4-4-4-4"/>
                                </svg>
                                <a href="{{ $fUrl }}" target="_blank" class="text-amber-700 hover:underline">
                                    {{ $fName }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div>
                    <p class="text-xs text-slate-500 mb-1">No Attachments Found</p>
                     
                </div>
            @endif
        </div>
    </div>
</div>
