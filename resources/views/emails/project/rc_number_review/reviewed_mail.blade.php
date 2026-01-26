@component('mail::message')
# Project Review Completed

Hello {{ $project->created_by->name ?? 'Project Owner' }},

Your project **{{ $project->name ?? 'Unnamed Project' }}** has been successfully reviewed.

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

You may now proceed with the next steps related to your project. If additional actions are required in the future, you will be notified accordingly.

@component('mail::button', ['url' => $viewUrl])
View Project
@endcomponent

If you have any questions, please contact the reviewing authority or log in to the system for more details.

Thank you for your cooperation.

Regards,  
{{ config('app.name') }}
@endcomponent


