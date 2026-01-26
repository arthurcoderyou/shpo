@component('mail::message')
# Changes Requested for Your Project

Hello {{ $project->created_by->name ?? 'Project Owner' }},

The review for your project **{{ $project->name ?? 'Unnamed Project' }}** has been completed.  
However, **changes are required** before the review process can proceed further.

---

## Review Details

- **Project:** {{ $project->name ?? 'N/A' }}
- **Review Status:** Reviewed
- **RC# :** {{ $project->rc_number ?? 'N/A' }}
- **Reviewed On:** {{ $reviewedAt }} ({{ $reviewedAtTz }})

---

@php
    $reviewNotes = $review->message ?? $review->notes ?? null;
@endphp

@if(!empty($reviewNotes))
### Reviewer Notes
{!! Str::markdown($reviewNotes) !!}
@endif

Please log in to the system and review the comments or requested changes provided by the reviewer. Once the necessary updates are made, you may resubmit the project for further review.

@component('mail::button', ['url' => $viewUrl])
View Project
@endcomponent

If you need clarification regarding the requested changes, please coordinate with the reviewing authority.

Thank you for your prompt attention to this matter.

Regards,  
{{ config('app.name') }}
@endcomponent

