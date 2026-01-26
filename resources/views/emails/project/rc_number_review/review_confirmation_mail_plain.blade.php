REVIEW SUBMITTED SUCCESSFULLY

Hello {{ $review->reviewer->name ?? 'Reviewer' }},

Your review for the project "{{ $project->name ?? 'Unnamed Project' }}" has been successfully submitted.

--------------------------------------------------
REVIEW SUMMARY
--------------------------------------------------
Project: {{ $project->name ?? 'N/A' }}
RC#: {{ $project->rc_number ?? 'N/A' }}
Review Status: {{ ucfirst(str_replace('_', ' ', $review->status)) }}
Submitted On: {{ $reviewedAt }} ({{ $reviewedAtTz }})

@php
    $reviewNotes = $review->message ?? $review->notes ?? null;
@endphp

@if(!empty($reviewNotes))
--------------------------------------------------
YOUR SUBMITTED NOTES
--------------------------------------------------
{{ $reviewNotes }}
@endif

No further action is required from you at this time unless the project is returned for re-review.

View the project here:
{{ $viewUrl }}

Thank you for your time and contribution to the review process.

Regards,
{{ config('app.name') }}
