{{ $statusLabel }} — {{ optional($projectDocument->document_type)->name ?? 'Project Document' }}

Project: {{ $project->name ?? '—' }}
Document: {{ $projectDocument->name ?? (optional($projectDocument->document_type)->name ?? '—') }}
Reviewed By: {{ optional($projectReviewer->user)->name ?? '—' }}
Submitted At: {{ $submittedAt ?? '—' }} ({{ $submittedAtTz ?? config('app.timezone') }})

------------------------------------------------------------

{!! strip_tags($statusMessage) !!}

@if(!empty($review->message) || !empty($review->notes))
------------------------------------------------------------
Reviewer Notes:
{{ $review->message ?? $review->notes }}
@endif

@if(!empty($resubmitRequirements))
------------------------------------------------------------
What you need to update:
{{ $resubmitRequirements }}
@endif

@if(!empty($hasNextUnapproved) && $hasNextUnapproved)
------------------------------------------------------------
Next Step:
Waiting on next reviewer: {{ $nextReviewerName ?? 'Open Reviewer' }}
No action is required from you at this time.
@endif

@if(!empty($dueAt))
------------------------------------------------------------
Resubmission due by: {{ $dueAt }} ({{ $dueAtTz ?? config('app.timezone') }})
@if(!empty($isSubmitter) && $isSubmitter === true)
Please ensure you submit the updated document before this deadline.
@endif
@endif

@if(!empty($viewUrl))
------------------------------------------------------------
View Review:
{{ $viewUrl }}
@endif

------------------------------------------------------------
If you have questions about this review, please contact your administrator.

Thanks,
{{ config('app.name') }}
