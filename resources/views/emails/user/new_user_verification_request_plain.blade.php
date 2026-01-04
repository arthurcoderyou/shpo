New User Role Verification Request

Hello {{ $userToNotify->name ?? 'No name' }},

A new user has submitted a role verification request and is waiting for your review.

------------------------------------------------------------

USER INFORMATION
- Name: {{ $user->name ?? 'Unknown User' }}
- Email: {{ $user->email ?? 'N/A' }}
- Requested On: {{ $submittedAt }} ({{ $submittedAtTz }})

------------------------------------------------------------

NEXT STEP REQUIRED
Please review and complete this user’s role verification request so they can proceed in the program.

Review User:
{{ $viewUrl }}

If the link above does not work, copy and paste it into your browser.

------------------------------------------------------------

Thank you,
{{ config('app.name') }} System
