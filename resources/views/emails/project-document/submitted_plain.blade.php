Project Document Submission Confirmation

Hello {{ $document->submitted_by->name ?? $project->submitter->name ?? 'User' }},

Your project document "{{ $document->title ?? 'Untitled Document' }}" was successfully submitted for review.

Project: {{ $project->name ?? 'Project' }}
Submission ID: {{ $document->id }}
Status: {{ ucfirst($document->status ?? 'submitted') }}
Submitted on: {{ optional($document->created_at)->timezone(config('app.timezone'))->format('F j, Y g:ia') ?? now()->timezone(config('app.timezone'))->format('F j, Y g:ia') }}

@php
use Illuminate\Support\Facades\Route;

$viewUrl =
    $document->url
    ?? ($document->show_url ?? null)
    ?? (Route::has('projects.documents.show') ? route('projects.documents.show', [$project, $document]) : null)
    ?? (Route::has('projects.show') ? route('projects.show', $project) : config('app.url'));
@endphp
@if($viewUrl)
View Submission: {{ $viewUrl }}
@endif

Questions? Reply to this email or contact {{ config('mail.from.address') }}.

Thanks,
{{ config('app.name') }} Team
{{ config('app.url') }}

--
You’re receiving this because you have an active account with {{ config('app.name') }}.
If you believe this was sent in error, please contact support.
