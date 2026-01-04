Open Review

An open review exists for the following project.  

Project: {{ $project->name ?? '—' }} 
Submitted: {{ $submittedAt }} ({{ $submittedAtTz }}) 

@isset($reviewUrl)
Open Review List: {{ $reviewUrl }}
@endisset

By claiming, your name will appear as the active reviewer and review timestamps will be recorded.

— {{ config('app.name') }}

@isset($unsubscribeUrl)
Unsubscribe: {{ $unsubscribeUrl }}
@endisset
