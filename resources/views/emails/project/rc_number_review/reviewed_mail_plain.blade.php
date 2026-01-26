PROJECT REVIEW COMPLETED

Hello {{ $project->created_by->name ?? 'Project Owner' }},

Your project "{{ $project->name ?? 'Unnamed Project' }}" has been successfully reviewed.

--------------------------------------------------
REVIEW DETAILS
--------------------------------------------------
Project: {{ $project->name ?? 'N/A' }}
Review Status: Reviewed
RC#: {{ $project->rc_number ?? 'N/A' }}
Reviewed On: {{ $reviewedAt }} ({{ $reviewedAtTz }})

@php
    $reviewNotes = $review->message ?? $review->notes ?? null;
@endphp

@if(!empty($reviewNotes))
--------------------------------------------------
REVIEWER NOTES
--------------------------------------------------
{{ $reviewNotes }}
@endif

You may now proceed with the next steps related to your project.
If additional actions are required in the future, you will be notified accordingly.

View your project here:
{{ $viewUrl }}

If you have any questions, please contact the reviewing authority or log in to the system for more details.

Thank you for your cooperation.

Regards,
{{ config('app.name') }}
