<x-mail::message>
# Time Settings Updated

The system-wide project time settings have been updated.

@if($user->can('system access global admin'))
### Admin Settings Updated
- Project Submission Restriction: **{{ $project_timer->project_submission_restrict_by_time ? 'Yes' : 'No' }}**
@endif

@if($user->can('system access reviewer'))
### Reviewer Timeline
- Reviewer Response Time: **{{ $project_timer->reviewer_response_duration }} {{ ucfirst($project_timer->reviewer_response_duration_type) }}**
@endif

@if($user->hasAnyPermission(['system access reviewer', 'system access admin', 'system access global admin']))
### Submitter Response Time

- Duration: **{{ $project_timer->submitter_response_duration }} {{ ucfirst($project_timer->submitter_response_duration_type) }}**
@endif

@if($user->can('system access user'))
### Submission Window
- Open: **{{ \Carbon\Carbon::parse($project_timer->project_submission_open_time)->format('F j, Y g:i A') }}**
- Close: **{{ \Carbon\Carbon::parse($project_timer->project_submission_close_time)->format('F j, Y g:i A') }}**
- Message: _{{ $project_timer->message_on_open_close_time }}_
@endif

@if($user->can('system access global admin') || $user->can('timer list view'))
<x-mail::button :url="$url">
View Timer Settings
</x-mail::button>
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
