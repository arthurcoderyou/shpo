Project Submission Confirmation

Hello {{ $project->submitter->name ?? $project->updator->name ?? 'User' }},

Your project "{{ $project->name ?? 'Untitled Project' }}" was successfully submitted.

------------------------------------------------------------
Project: {{ $project->name ?? 'Project' }}
Submitted on: {{ $submittedAt }}
------------------------------------------------------------

Your project is now in the review queue and will be evaluated by the administrator.
You will receive further updates once the evaluation is complete or if additional information is needed.

You can view your project here:
{{ $viewUrl }}

If you have any questions, you may reply to this email or contact  {{ config('mail.from.address') }}.

Thank you,
{{ config('app.name') }} Team
{{ config('app.url') }}

------------------------------------------------------------
You are receiving this notification because you have an active account with 
{{ config('app.name') }}. If you believe this was sent in error, please contact support.
------------------------------------------------------------
