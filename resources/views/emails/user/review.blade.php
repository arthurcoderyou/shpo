{{-- <x-mail::message>
# Introduction

The body of your message.

<x-mail::button :url="''">
Button Text
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message> --}}


@component('mail::message')
# User Verification Request

A new user has completed their initial verification and now requires admin approval to update their role.

## User Details:
- **Name:** {{ $new_user->name }}  
- **Email:** {{ $new_user->email }}  
- **Role Request:** {{ $new_user->role_request }}  
- **Registered On:** {{ $new_user->created_at->format('F j, Y g:i A') }}  

@component('mail::panel')
Please review the user’s details and verify their account to grant appropriate access.
@endcomponent

@component('mail::button', ['url' => $url])
Verify User
@endcomponent

Thank you,  
{{ config('app.name') }}
@endcomponent