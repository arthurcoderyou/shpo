 
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
# Project Completed Approval Process
 
Hello {{ $user->name }} 🎉,

We are excited to inform you that your project has been successfully approved.  
  
The reviewer list for the project **{{ $project->name }}** are listed below:

## **Updated Reviewer List and Order:**
@foreach ($project->project_documents as $project_document)

{{ $project_document->document_type->name }}

@foreach ($project_document->project_reviewers as $project_reviewer)
- **{{ $project_reviewer->order }}.** {{ $project_reviewer->user->name }} || Review Status: {{ ucfirst($project_reviewer->review_status) }}
@endforeach


@endforeach

 

## **Project Details:**
- **Name:** {{ $project->name }}
- **Description:** {{ $project->description }}
- **Status:** {{ ucfirst($project->status) }}

@component('mail::button', ['url' => $url])
View Project
@endcomponent
 

Thanks,  
{{ config('app.name') }}
@endcomponent