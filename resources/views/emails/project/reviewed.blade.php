@php
use Illuminate\Support\Facades\Route;

$viewUrl =
    $project->url
    ?? ($project->show_url ?? null)
    ?? (Route::has('projects.show') ? route('projects.show', $project) : config('app.url'));

$reviewedAt = ($project->updated_at ?? now())
    ->timezone(config('app.timezone'))
    ->format('F j, Y g:ia');
@endphp

@component('mail::message')
# Project Review Completed

Hello {{ $project->submitter->name ?? $project->updator->name ?? 'User' }},

Your project **“{{ $project->name ?? 'Untitled Project' }}”** has now been **reviewed** by our team.

@component('mail::panel')
**Project Name:** {{ $project->name ?? 'Project' }}  
**Reviewed On:** {{ $reviewedAt }}
@endcomponent

The review for this project has been completed.  
You may now proceed on follow-up actions.   

@isset($viewUrl)
@component('mail::button', ['url' => $viewUrl])
View Project
@endcomponent
@endisset

If you have any questions or need assistance, please feel free to reply to this message or contact us at  
**{{ config('mail.from.address') }}**.

Thank you,  
**{{ config('app.name') }} Team**  
[{{ config('app.url') }}]({{ config('app.url') }})

---

You are receiving this email because you are subscribed to updates for this project.  
If you believe you received this message in error, please contact support.
@endcomponent
