FOLLOW-UP REVIEW REQUEST

Hi {{ !empty($projectReviewer->user->name) ? $projectReviewer->user->name : 'Reviewer' }},

A project you previously reviewed has been resubmitted by the submitter and
requires your follow-up review.

Project: {{ $project->name ?? '—' }}
Resubmitted: {{ $submittedAt }} ({{ $submittedAtTz }})
@isset($deadlineAt)
Due: {{ $deadlineAt }}
@endisset

@isset($reviewUrl)
Review Page:
{{ $reviewUrl }}
@endisset

Please review the updated document and record your decision.
If you have questions, use the internal comments on the review page instead of
replying to this email.

Regards,
{{ config('app.name') }}

------------------------------------------------------------

Notification Preferences
@isset($reviewUrl)
Manage notifications from your account preferences after opening the review page.
@endisset
@isset($unsubscribeUrl)
Unsubscribe from these emails:
{{ $unsubscribeUrl }}
@endisset
