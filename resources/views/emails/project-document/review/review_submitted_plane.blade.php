{{ $statusLabel ?? 'Review Submitted' }}

Hello {{ $recipientName ?? 'Reviewer' }},

{!! strip_tags($statusMessage ?? 'Your review has been recorded.') !!}

Project: {{ $project->name ?? '—' }}
Document: {{ optional($projectDocument->document_type)->name ?? 'Project Document' }}
Current Status: {{ ucfirst($status ?? 'reviewed') }}
Submitted At: {{ $submittedAt ?? '—' }} ({{ $submittedAtTz ?? config('app.timezone') }})

@if(!empty($hasNextUnapproved)  && $status == "approved")
Next Reviewer: {{ $nextReviewerName ?? 'Open Reviewer' }}
@endif

@if(!empty($viewUrl))
Open Project Document: {{ $viewUrl }}
@endif

Details:
- Project ID: {{ $project->id ?? '—' }}
- Document ID: {{ $projectDocument->id ?? '—' }}
- Reviewer Slot: #{{ $projectReviewer->id ?? '—' }}

@if(!empty($review?->notes))
Reviewer Notes:
{{ Str::limit(strip_tags($review->notes), 500) }}
@endif

Thanks,
{{ config('app.name') }}
