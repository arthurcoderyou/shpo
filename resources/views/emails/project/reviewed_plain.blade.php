Project Review Completed

Hello {{ $project->submitter->name ?? $project->updator->name ?? 'User' }},

Your project "{{ $project->name ?? 'Untitled Project' }}" has now been reviewed by our team.

Project Name: {{ $project->name ?? 'Project' }}
Reviewed On: {{ $reviewedAt }}

The review for this project has been completed. You may now proceed with any follow-up actions as needed.

You can view the project here:
{{ $viewUrl ?? config('app.url') }}

If you have any questions or need assistance, you may reply directly to this email or contact us at:
{{ config('mail.from.address') }}

Thank you,
{{ config('app.name') }} Team
{{ config('app.url') }}

------------------------------------------------------------

You are receiving this email because you are subscribed to updates for this project.
If you believe you received this message in error, please contact support.
