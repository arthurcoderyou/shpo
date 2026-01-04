@component('mail::message')
# Your Account Has Been Verified

Hello {{ $user->name ?? 'User' }},

Good news! Your account has been **successfully verified** and your access has now been activated.

You may now log in and proceed using your assigned permissions and program features.

## What You Can Do Now:
✅ Access your personalized dashboard  
✅ Manage your account settings  
✅ Utilize all available features  


@component('mail::button', ['url' => $viewUrl])
Go to Website
@endcomponent

If the button does not work, you can visit this link:

{{ config('app.url') }}

---

Thank you,  
**{{ config('app.name') }} Team**
@endcomponent
