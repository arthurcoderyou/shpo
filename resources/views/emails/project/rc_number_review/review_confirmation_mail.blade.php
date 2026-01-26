@component('mail::message')
# Review Submitted Successfully

Hello {{ $review->created_by->name ?? 'Reviewer' }},

Your review for the project **{{ $project->name ?? 'Unnamed Project' }}** has been successfully submitted.

---

## Review Summary

- **Project:** {{ $project->name ?? 'N/A' }}
- **RC#:** {{ $project->rc_number ?? 'N/A' }}
- **Review Status:** {{ ucfirst(str_replace('_', ' ', $review->status)) }}
- **Submitted On:** {{ $reviewedAt }} ({{ $reviewedAtTz }})

---

@php
    $reviewNotes = $review->message ?? $review->notes ?? null;
@endphp

@if(!empty($reviewNotes))
### Your Submitted Notes
{!! Str::markdown($reviewNotes) !!}
@endif

No further action is required from you at this time unless the project is returned for re-review.

@component('mail::button', ['url' => $viewUrl])
View Project
@endcomponent

Thank you for your time and contribution to the review process.

Regards,  
{{ config('app.name') }}
@endcomponent
