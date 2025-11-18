@component('mail::message')
{{-- Header --}}
# {{ $statusLabel ?? 'Re-review Requested' }}

{!! $statusMessage ?? 'A <strong>re-review</strong> has been requested. Please review the request and provide your decision.' !!}

{{-- Context summary --}}
@php
  $safeProject     = $projectName ?? optional($project)->name ?? 'Project';
  $safeDocType     = $docTypeName ?? optional(optional($projectDocument)->document_type)->name ?? 'Project Document';
  $recipientName   = $recipientName ?? optional($projectReviewer->user)->name ?? optional($projectReviewer)->name ?? 'Reviewer';
  $requesterName   = $requestedByName ?? 'Requester';
  $when            = $requestedAt ?? null;
  $tz              = $requestedAtTz ?? config('app.timezone', 'UTC');
@endphp

**Project:** {{ $safeProject }}  
**Document:** {{ $safeDocType }}  
**Requested Re-review back to:** {{ $recipientName }}  
**Requested By:** {{ $requesterName }}  
@if($when)
**Requested At:** {{ $when }} ({{ $tz }})
@endif

@if(!empty($reason))
> **Reason for Re-review**  
> {!! nl2br(e($reason)) !!}
@endif

@isset($viewUrl)
@component('mail::button', ['url' => $viewUrl])
Open Re-review
@endcomponent
@endisset

---

### What to do next
1. Open the document page.  
2. Review notes and context.  
3. Record your decision (Approve / Reject).

@if(!empty($additionalNotes))
{{ $additionalNotes }}
@endif

Thanks,  
{{ config('app.name') }}

{{-- Optional footer note --}}
@isset($unsubscribeUrl)
<hr>
<small>
If you prefer not to receive these notifications, you can <a href="{{ $unsubscribeUrl }}">unsubscribe</a>.
</small>
@endisset
@endcomponent
