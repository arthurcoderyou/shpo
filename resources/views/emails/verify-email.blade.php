@component('mail::message')
# Verify Your Email

Hello {{ $user->name }},

Thank you for signing up! Please verify your email by clicking the button below.

@component('mail::button', ['url' => $verificationUrl])
Verify Email
@endcomponent

If you did not create this account, you can ignore this email.

Thanks,  
{{ config('app.name') }}
@endcomponent