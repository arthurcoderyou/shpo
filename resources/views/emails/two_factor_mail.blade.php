<x-mail::message>
# Two-Factor Authentication

Your OTP code is: <strong>{{ $otp_code }}</strong>

<x-mail::button :url="$url">
Verify OTP Code
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
