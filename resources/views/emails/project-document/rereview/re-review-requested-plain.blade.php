{{ $statusLabel ?? 'Re-review Requested' }}

{{ strip_tags($statusMessage ?? 'A re-review has been requested. Please review the request and provide your decision.') }}

------------------------------------------------------------
CONTEXT DETAILS
------------------------------------------------------------

Project: {{ $safeProject ?? ($projectName ?? optional($project)->name ?? 'Project') }}
Document: {{ $safeDocType ?? ($docTypeName ?? optional(optional($projectDocument)->document_type)->name ?? 'Project Document') }}
Requested Re-review back to: {{ $recipientName ?? (optional($projectReviewer->user)->name ?? optional($projectReviewer)->name ?? 'Reviewer') }}
Requested By: {{ $requesterName ?? 'Requester' }}

@if(!empty($when))
Requested At: {{ $when }} ({{ $tz ?? config('app.timezone', 'UTC') }})
@endif

@if(!empty($reason))
------------------------------------------------------------
REASON FOR RE-REVIEW
------------------------------------------------------------
{{ $reason }}
@endif

@if(!empty($viewUrl))
------------------------------------------------------------
Open Re-review:
{{ $viewUrl }}
@endif

------------------------------------------------------------
WHAT TO DO NEXT
------------------------------------------------------------

1. Open the document page.
2. Review notes and context.
3. Record your decision (Approve / Reject).

@if(!empty($additionalNotes))
Additional Notes:
{{ $additionalNotes }}
@endif

Thanks,  
{{ config('app.name') }}

@if(!empty($unsubscribeUrl))
------------------------------------------------------------
To stop receiving these notifications, unsubscribe here:
{{ $unsubscribeUrl }}
@endif
