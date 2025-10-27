@component('mail::message')
# Follow-Up Review Request

Hi @if(!empty($projectReviewer->user->name)) **{{ $projectReviewer->user->name }}** @else Reviewer @endif,

A project document you previously reviewed has been **resubmitted by the submitter** and needs your **follow-up review**.

@component('mail::panel')
**Project:** {{ $project->name ?? '—' }}  
**Document:** {{ $document->document_type->name ?? '—' }}  
**Resubmitted:** {{ $submittedAt }} ({{ $submittedAtTz }})  
@isset($deadlineAt)
**Due:** {{ $deadlineAt }}
@endisset
@endcomponent

@isset($reviewUrl)
@component('mail::button', ['url' => $reviewUrl])
Open Review Page
@endcomponent
@endisset

Please review the updated document and record your decision (Approve / Changes Requested).  
If you have questions, kindly use the internal comments on the review page instead of replying to this email.

Thanks,  
{{ config('app.name') }}

@slot('subcopy')
If you no longer want to receive these review notifications:
@isset($reviewUrl)
- Manage notifications from your account preferences after opening the review page.
@endisset
@isset($unsubscribeUrl)
- Or click to unsubscribe: <{{ $unsubscribeUrl }}>
@endisset
@endslot
@endcomponent
