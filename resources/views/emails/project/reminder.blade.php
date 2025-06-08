@component('mail::message')
# Project Submission Reminder

Hello {{ $project->creator->name }},

This is a friendly reminder that your project titled:

**"{{ $project->name }}"**

is scheduled for submission today ({{ now()->format('l, F j, Y') }}). Today is a working day, and submissions are currently open during working hours.

@if($message)
**Scheduled Submission Open and Close Time:**  {{ $message }}
@endif

@component('mail::button', ['url' => route('project.show', $project->id)])
View Project
@endcomponent

If you've already completed your project, no further action is needed. Otherwise, please ensure it is ready before the submission window ends.

Thanks,  
{{ config('app.name') }}
@endcomponent
