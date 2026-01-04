@php
use Illuminate\Support\Facades\Route;

$viewUrl =
    $project->url
    ?? ($project->show_url ?? null)
    ?? (Route::has('projects.show') ? route('projects.show', $project) : config('app.url'));

$submittedAt = ($project->submitted_at ?? $project->updated_at ?? now())
    ->timezone(config('app.timezone'))
    ->format('F j, Y g:ia');
@endphp

@component('mail::message')
# Project Submission Confirmation

Hello {{ $project->submitter->name ?? $project->updator->name ?? 'User' }},

Your project **“{{ $project->name ?? 'Untitled Project' }}”** was **successfully submitted**.

@component('mail::panel')
**Project:** {{ $project->name ?? 'Project' }}  
**Submitted on:** {{ $submittedAt }}
@endcomponent

Your project is now in the review queue and will be **evaluated by the administrator**.  
You will receive further updates once the evaluation is complete or if additional information is required.

@isset($viewUrl)
@component('mail::button', ['url' => $viewUrl])
View Project
@endcomponent
@endisset

If you have any questions, you may reply directly to this email or contact **{{ config('mail.from.address') }}**.

Thanks,  
**{{ config('app.name') }} Team**  
[{{ config('app.url') }}]({{ config('app.url') }})

---

You are receiving this notification because you have an active account with **{{ config('app.name') }}**.  
If you believe this message was sent in error, please contact support.
@endcomponent
