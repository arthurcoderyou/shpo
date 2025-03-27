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
# Project Reviewed 

Hello {{ $project->creator->name }},

Your project **{{ $project->name }}** has been reviewed.

## Review Details:
- **Project:** {{ $project->name }}
- **Review Status:** {{ ucfirst($review->review_status) }}
- **Reviewed On:** {{ $review->created_at->format('F j, Y \a\t g:i A') }}
 

@component('mail::panel')
{{ $review->project_review }}
@endcomponent

@component('mail::button', ['url' => $url])
View Reviewed Project
@endcomponent

Thank you,  
{{ config('app.name') }}
@endcomponent

