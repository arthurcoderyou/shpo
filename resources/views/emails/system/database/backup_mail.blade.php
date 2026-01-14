{{-- resources/views/emails/system/database/backup_mail.blade.php --}}

@component('mail::message')
# Database Backup Notification

Hello {{ $targetUser->name ?? 'User' }},

This is an automated notification confirming that a database backup for **{{ config('app.name') }}** has been created successfully.

@component('mail::panel')
**Backup Details**

@isset($dbBackup->id)
- Backup ID: {{ $dbBackup->id }}
@endisset

@isset($dbBackup->created_at)
- Created At: {{ $dbBackup->created_at->format('M d, Y h:i A') }}
@endisset

@isset($dbBackup->file_name)
- File Name: {{ $dbBackup->file }}
@endisset

@isset($dbBackup->file_path)
- Storage Path: {{ $dbBackup->folder }}
@endisset
@endcomponent

If you did not expect this email, please contact your system administrator.

Thanks,  
{{ config('mail.from.name') ?? config('app.name') }}
@endcomponent
