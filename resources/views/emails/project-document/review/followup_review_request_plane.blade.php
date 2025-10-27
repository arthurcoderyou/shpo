Follow-Up Review Request

Hi @if(!empty($projectReviewer->user->name)) {{ $projectReviewer->user->name }} @else Reviewer @endif,

A project document you previously reviewed has been resubmitted by the submitter and needs your follow-up review.

Project: {{ $project->name ?? '—' }}
Document: {{ $document->document_type->name ?? '—' }}
Resubmitted: {{ $submittedAt }} ({{ $submittedAtTz }})
@isset($deadlineAt)
Due: {{ $deadlineAt }}
@endisset

@if(!empty($reviewUrl))
Open the review page:
{{ $reviewUrl }}
@endif

Please review the updated document and record your decision (Approve / Changes Requested).
If you have questions, please use the internal comments on the review page instead of replying to this email.

Thanks,
{{ config('app.name') }}

@if(!empty($reviewUrl) || !empty($unsubscribeUrl))
---
Notification Preferences:
@isset($reviewUrl)
- Manage notifications from your account preferences after opening the review page.
@endisset
@isset($unsubscribeUrl)
- Unsubscribe: {{ $unsubscribeUrl }}
@endisset
@endif
