<x-mail::message>
# Project Time Settings Updated

Hello {{ $targetUser->name }},

A quick heads-up: the project time settings were updated. Here’s what matters for your reviews.

### Your reviewer response time
- **{{ $projectTimer->reviewer_response_duration }} {{ ucfirst($projectTimer->reviewer_response_duration_type) }}**

### Submitter response time (for context)
- **{{ $projectTimer->submitter_response_duration }} {{ ucfirst($projectTimer->submitter_response_duration_type) }}**

@if($projectTimer->project_submission_restrict_by_time)
### Submission window (submissions are time-restricted)
- Open: **{{ \Carbon\Carbon::parse($projectTimer->project_submission_open_time)->format('F j, Y g:i A') }}**
- Close: **{{ \Carbon\Carbon::parse($projectTimer->project_submission_close_time)->format('F j, Y g:i A') }}**
@endif

{{-- @if(!empty($viewUrl))
<x-mail::button :url="$viewUrl">
View Details
</x-mail::button>
@endif --}}

Thank you,<br>
{{ config('app.name') }}
</x-mail::message>
