<x-mail::message>
# Time Settings Updated

Hello {{ $targetUser->name }},

Project time settings have been updated.

{{-- @if(!empty($viewUrl))
<x-mail::button :url="$viewUrl">
View Details
</x-mail::button>
@endif --}}

Regards,<br>
{{ config('app.name') }}
</x-mail::message>
