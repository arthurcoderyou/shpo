PROJECT SUBMISSION TIMES UPDATED

Hello {{ $targetUser->name }},

We updated the project submission time settings.

@if($projectTimer->project_submission_restrict_by_time)
When submissions are open:
- Open: {{ \Carbon\Carbon::parse($projectTimer->project_submission_open_time)->format('F j, Y g:i A') }}
- Close: {{ \Carbon\Carbon::parse($projectTimer->project_submission_close_time)->format('F j, Y g:i A') }}
@if(!empty($projectTimer->message_on_open_close_time))
{{ $projectTimer->message_on_open_close_time }}
@endif
@else
Submissions are currently not restricted by time.
@endif

{{-- @if(!empty($viewUrl))
View schedule: {{ $viewUrl }}
@endif --}}

Thanks,
{{ config('app.name') }}
