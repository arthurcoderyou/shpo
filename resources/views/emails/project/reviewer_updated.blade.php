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
# Project Reviewer List Updated

Hello {{ $user->name }},

The reviewer list for the project **{{ $project->name }}** has been updated. Below is the new list of assigned reviewers:

## **Updated Reviewer List and Order:**
@foreach ($project->project_documents as $project_document)

{{ $project_document->document_type->name }}

@foreach ($project_document->project_reviewers as $project_reviewer)
- **{{ $project_reviewer->order }}.** {{ $project_reviewer->user->name }} || Review Status: {{ ucfirst($project_reviewer->review_status) }}
@endforeach


@endforeach


@php
    $current_reviewer = $project->getCurrentReviewer();
@endphp 

@if(!empty($current_reviewer))
## **Current Reviewer:**
- **{{ $current_reviewer->order }}.** {{ $current_reviewer->user->name }}
@endif


## **Project Details:**
- **Name:** {{ $project->name }}
- **Description:** {{ $project->description }}
- **Status:** {{ ucfirst($project->status) }}

@component('mail::button', ['url' => $url])
View Project
@endcomponent

Please review the project as necessary.

Thanks,  
{{ config('app.name') }}
@endcomponent