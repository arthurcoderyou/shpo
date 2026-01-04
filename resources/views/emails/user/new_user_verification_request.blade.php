@component('mail::message')
# New User Role Verification Request

Hello {{ $userToNotify->name ?? 'No name' }},

A new user has submitted a **role verification request** and is waiting for your review.

---

### **User Information**
- **Name:** {{ $user->name ?? 'Unknown User' }}
- **Email:** {{ $user->email ?? 'N/A' }}
- **Requested On:** {{ $submittedAt }} ({{ $submittedAtTz }})

---

### **Next Step Required**
Please review and complete this user’s role verification request so they can proceed in the program.

@component('mail::button', ['url' => $viewUrl])
Review User
@endcomponent

If the button above does not work, you may copy and paste this link into your browser:

{{ $viewUrl }}

---

Thank you,  
**{{ config('app.name') }} System**
@endcomponent
