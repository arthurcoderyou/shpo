@component('mail::message')
# {{ $statusLabel ?? 'Review Submitted' }}

Hello {{ $recipientName ?? 'Reviewer' }},

{!! $statusMessage ?? 'Your review has been recorded.' !!}

@component('mail::panel')
**Project:** {{ $project->name ?? '—' }}  
**Document:** {{ optional($projectDocument->document_type)->name ?? 'Project Document' }}  
**Current Status:** {{ ucfirst($status ?? 'reviewed') }}  
**Submitted At:** {{ $submittedAt ?? '—' }} ({{ $submittedAtTz ?? config('app.timezone') }})
@endcomponent

@if(!empty($hasNextUnapproved) && $status == "approved")
### Next Reviewer
- **Name:** {{ $nextReviewerName ?? 'Open Reviewer' }}
@endif

@if(!empty($isActionable) && !empty($viewUrl))
@component('mail::button', ['url' => $viewUrl])
Review Now
@endcomponent
@elseif(!empty($viewUrl))
You can view the document here:

@component('mail::button', ['url' => $viewUrl])
Open Project Document
@endcomponent
@endif

---

**Details**

- **Project ID:** {{ $project->id ?? '—' }}
- **Document ID:** {{ $projectDocument->id ?? '—' }}
- **Reviewer Slot:** #{{ $projectReviewer->id ?? '—' }}

@if(!empty($review?->notes))
> **Reviewer Notes:**  
> {{ Str::limit(strip_tags($review->notes), 500) }}
@endif

Thanks,  
{{ config('app.name') }}

@endcomponent
