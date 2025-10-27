@php
use Illuminate\Support\Facades\Route;

$viewUrl =
    $document->url
    ?? ($document->show_url ?? null)
    ?? (Route::has('projects.documents.show') ? route('projects.documents.show', [$project, $document]) : null)
    ?? (Route::has('projects.show') ? route('projects.show', $project) : config('app.url'));

$submittedAt = optional($document->created_at)
    ? $document->created_at->timezone(config('app.timezone'))->format('F j, Y g:ia')
    : now()->timezone(config('app.timezone'))->format('F j, Y g:ia');
@endphp

@component('mail::message')
# Project Document Submission Confirmation

Hello {{ $document->submitted_by->name ?? $project->submitter->name ?? 'User' }},

Your project document **“{{ $document->title ?? 'Untitled Document' }}”** was **successfully submitted** for review.

@component('mail::panel')
**Project:** {{ $project->name ?? 'Project' }}  
**Submission ID:** {{ $document->id }}  
**Status:** {{ ucfirst($document->status ?? 'submitted') }}  
**Submitted on:** {{ $submittedAt }}
@endcomponent

Our review team will now assess your submission. You’ll receive an update when the review is complete or if more information is required.

@isset($viewUrl)
@component('mail::button', ['url' => $viewUrl])
View Submission
@endcomponent
@endisset

If you have questions, reply to this email or contact **{{ config('mail.from.address') }}**.

Thanks,  
**{{ config('app.name') }} Team**  
[{{ config('app.url') }}]({{ config('app.url') }})

---

You’re receiving this because you have an active account with **{{ config('app.name') }}**.  
If you believe this was sent in error, please contact support.
@endcomponent
