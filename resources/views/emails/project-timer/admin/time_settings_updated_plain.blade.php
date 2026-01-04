TIME SETTINGS UPDATED (ADMIN NOTICE)

Hello {{ $targetUser->name }},

This is an administrative notification that the system-wide Project Timer configuration has been updated.
@if(!empty($updatedAt))
Updated: {{ $updatedAt }}@if(!empty($updatedAtTz)) ({{ $updatedAtTz }})@endif
@endif

EFFECTIVE CONFIGURATION SUMMARY

Submission Restriction
- Restrict submissions by time: {{ $projectTimer->project_submission_restrict_by_time ? 'Enabled' : 'Disabled' }}

Reviewer Timeline
- Reviewer response time: {{ $projectTimer->reviewer_response_duration }} {{ ucfirst($projectTimer->reviewer_response_duration_type) }}

Submitter Timeline
- Submitter response time: {{ $projectTimer->submitter_response_duration }} {{ ucfirst($projectTimer->submitter_response_duration_type) }}

Submission Window
- Open: {{ \Carbon\Carbon::parse($projectTimer->project_submission_open_time)->format('F j, Y g:i A') }}
- Close: {{ \Carbon\Carbon::parse($projectTimer->project_submission_close_time)->format('F j, Y g:i A') }}
@if(!empty($projectTimer->message_on_open_close_time))
- Message shown to users: {{ $projectTimer->message_on_open_close_time }}
@endif

@if(!empty($viewUrl))
View Timer Settings: {{ $viewUrl }}
@endif

Regards,
{{ config('app.name') }}
