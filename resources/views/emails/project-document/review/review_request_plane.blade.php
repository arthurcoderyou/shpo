Review Requested

Reviewer: @if(!empty($projectReviewer->user->name)) {{ $projectReviewer->user->name }} @else (no name) @endif
Project: {{ $project->name ?? '—' }}
Document: {{ $document->document_type->name ?? '—' }}
Submitted: {{ $submittedAt }} ({{ $submittedAtTz }})
@isset($deadlineAt)
Due: {{ $deadlineAt }}
@endisset

@isset($reviewUrl)
Open Review Page: {{ $reviewUrl }}
@endisset

Please review the document and record your decision (Approve / Changes Requested / Reject).
Do not reply to this email. Use the internal comments on the review page.

— {{ config('app.name') }}

@isset($unsubscribeUrl)
Unsubscribe (One-Click): {{ $unsubscribeUrl }}
@endisset
