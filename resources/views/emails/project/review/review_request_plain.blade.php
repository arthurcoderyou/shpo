Review Requested

Hi {{ $projectReviewer->user->name ?? 'Reviewer' }},

A project document requires your review.

Project: {{ $project->name ?? '—' }}
Submitted: {{ $submittedAt }} ({{ $submittedAtTz }})
@if(!empty($deadlineAt))
Due: {{ $deadlineAt }}
@endif

@if(!empty($reviewUrl))
Open the review page:
{{ $reviewUrl }}
@endif

Please review the document and record your decision (Approve or Changes Requested).
If you have any questions, please use the internal comments on the review page instead of replying to this email.

Thank you,
{{ config('app.name') }}

------------------------------
Notification Preferences
------------------------------

@if(!empty($reviewUrl))
You can manage your notification settings from your account preferences after opening the review page.
@endif

@if(!empty($unsubscribeUrl))
To unsubscribe from these emails, click below:
{{ $unsubscribeUrl }}
@endif
