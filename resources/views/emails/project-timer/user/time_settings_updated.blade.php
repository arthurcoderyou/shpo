<x-mail::message>
# Project Submission Times Updated

Hello {{ $targetUser->name }},

We updated the project submission time settings. This helps keep reviews and submissions on schedule.

@if($projectTimer->project_submission_restrict_by_time)
### When submissions are open
- Open: **{{ \Carbon\Carbon::parse($projectTimer->project_submission_open_time)->format('F j, Y g:i A') }}**
- Close: **{{ \Carbon\Carbon::parse($projectTimer->project_submission_close_time)->format('F j, Y g:i A') }}**

@if(!empty($projectTimer->message_on_open_close_time))
_{{ $projectTimer->message_on_open_close_time }}_
@endif
@else
### Submissions
Submissions are currently **not restricted by time**.
@endif

{{-- @if(!empty($viewUrl))
<x-mail::button :url="$viewUrl">
View Submission Schedule
</x-mail::button>
@endif --}}

Thanks,<br> 
{{ config('app.name') }}
</x-mail::message>
