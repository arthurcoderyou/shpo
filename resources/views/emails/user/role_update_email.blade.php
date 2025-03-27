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
# Welcome, {{ $user->name }}! 🎉

We are excited to inform you that your account has been successfully verified, and your role has been updated. You now have full access to all the features of {{ config('app.name') }}.

## What You Can Do Now:
✅ Access your personalized dashboard  
✅ Manage your account settings  
✅ Utilize all available features  

@component('mail::panel')
If you have any questions, feel free to reach out to our support team.  
We’re here to help!
@endcomponent

@component('mail::button', ['url' => route('dashboard')])
Go to Dashboard
@endcomponent

Enjoy exploring the platform! 🚀  

Thanks,  
{{ config('app.name') }}
@endcomponent
