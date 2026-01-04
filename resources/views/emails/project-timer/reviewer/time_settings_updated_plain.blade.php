PROJECT TIME SETTINGS UPDATED

Hello {{ $targetUser->name }},

The project time settings were updated. Here’s what matters for your reviews:

- Reviewer response time: {{ $projectTimer->reviewer_response_duration }} {{ ucfirst($projectTimer->reviewer_response_duration_type) }}
- Submitter response time (context): {{ $projectTimer->submitter_response_duration }} {{ ucfirst($projectTimer->submitter_response_duration_type) }}

@if($projectTimer->project_submission_restrict_by_time)
Submission window (time-restricted)
- Open: {{ \Carbon\Carbon::parse($projectTimer->project_submission_open_time)->format('F j, Y g:i A') }}
- Close: {{ \Carbon\Carbon::parse($projectTimer->project_submission_close_time)->format('F j, Y g:i A') }}
@endif

{{-- @if(!empty($viewUrl))
View details: {{ $viewUrl }}
@endif --}}

Thank you,
{{ config('app.name') }}
