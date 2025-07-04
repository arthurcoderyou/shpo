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
# Project Review Request

{{-- Hello {{ $reviewer->user->name }}, --}}

You have been assigned as a reviewer for the project **{{ $project->name }}**.

## Project Details:
- **Title:** {{ $project->name }} 
- **Status:**  
@switch($project->status)
    @case('submitted')
        <span style="color: blue;"><strong>Submitted</strong></span>
        @break

    @case('in_review')
        <span style="color: orange;"><strong>In Review</strong></span>
        @break

    @case('approved')
        <span style="color: green;"><strong>Approved</strong> 🎉</span>
        @break

    @case('rejected')
        <span style="color: red;"><strong>Rejected</strong></span>
        @break

    @case('completed')
        <span style="color: purple;"><strong>Completed</strong></span>
        @break

    @case('cancelled')
        <span style="color: gray;"><strong>Cancelled</strong></span>
        @break

    @default
        <span style="color: black;"><strong>{{ $project->status_text }}</strong></span>
@endswitch
- **Document:** {{ $reviewer->project_document->document_type->name ?? "" }} 

- **Submitted by:** {{ $project->creator->name }}

@component('mail::panel')
{{ $project->description }}
@endcomponent

@component('mail::button', ['url' => $url])
View Project
@endcomponent

Please review the project and provide your feedback.

Thanks,  
{{ config('app.name') }}
@endcomponent
