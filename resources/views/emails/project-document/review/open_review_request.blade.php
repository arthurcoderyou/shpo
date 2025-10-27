@component('mail::message')
# Open Review

An open review exists for the following document. 

@component('mail::panel')
**Project:** {{ $project->name ?? '—' }}  
**Document:** {{ $document->document_type->name ?? '—' }}  
**Submitted:** {{ $submittedAt }} ({{ $submittedAtTz }})   
@endcomponent

@isset($reviewUrl)
@component('mail::button', ['url' => $reviewUrl])
Open Review List
@endcomponent
@endisset

> By claiming, your name will appear as the active reviewer and review timestamps will be recorded.

Thanks,  
{{ config('app.name') }}

@slot('subcopy')
You received this because you have administrator privileges for project reviews.
@isset($unsubscribeUrl)
Unsubscribe from admin open-review notices: <{{ $unsubscribeUrl }}>
@endisset
@endslot
@endcomponent
