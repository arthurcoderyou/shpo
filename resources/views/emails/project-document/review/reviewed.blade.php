@component('mail::message')
# {{ $statusLabel }} — {{ optional($projectDocument->document_type)->name ?? 'Project Document' }}

**Project:** {{ $project->name ?? '—' }}  
**Document:** {{ $projectDocument->name ?? (optional($projectDocument->document_type)->name ?? '—') }}  
**Reviewed By:** {{ optional($projectReviewer->user)->name ?? '—' }}  
**Submitted At:** {{ $submittedAt ?? '—' }} ({{ $submittedAtTz ?? config('app.timezone') }})

---

> {!! Str::markdown($statusMessage) !!}

@php
    $reviewNotes = $review->message ?? $review->notes ?? null;
@endphp

@if(!empty($reviewNotes))
### Reviewer Notes
{!! Str::markdown($reviewNotes) !!}
@endif

@if(!empty($resubmitRequirements))
### What you need to update
{{ $resubmitRequirements }}
@endif

@if(!empty($hasNextUnapproved) && $hasNextUnapproved && empty($resubmitRequirements))
@component('mail::panel')
**Next Step:** Waiting on next reviewer:  
**{{ $nextReviewerName ?? 'Open Reviewer' }}**  
No action is required from you at this time.
@endcomponent
@endif

@if(!empty($dueAt))
> **Resubmission due by:** **{{ $dueAt }}** ({{ $dueAtTz ?? config('app.timezone') }}).  
@if(!empty($isSubmitter) && $isSubmitter === true)
Please ensure you submit the updated document before this deadline.
@endif
@endif

@isset($viewUrl)
@component('mail::button', ['url' => $viewUrl])
{{ (isset($status) && $status === 'changes_requested' && !empty($isSubmitter) && $isSubmitter) ? 'View Review & Resubmit' : 'View Review' }}
@endcomponent
@endisset

@component('mail::panel')
If you have questions about this review, please reply to this email or contact your administrator.
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
