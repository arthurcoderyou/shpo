 
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
# Project Document Completed Approval Process
 
Hello {{ $user->name }} 🎉,

We are excited to inform you that your project docuemnt **{{ $project_document->document_type->name }}** for **{{ $project_document->project->name }}** has been successfully approved.  
  
The reviewer list for the project **{{ $project_document->document_type->name }}** are listed below:

## **Updated Reviewer List and Order:**
 
@foreach ($project_document->project_reviewers as $project_reviewer)
- **{{ $project_reviewer->order }}.** {{ $project_reviewer->user->name }} || Review Status: {{ ucfirst($project_reviewer->review_status) }}
@endforeach

  
## **Project Document Details:**
- **Document Name:** {{ $project_document->document_type->name }}
- **Project Name:** {{ $project_document->project->name }} 
- **Description:** {{ $project_document->project->description }}
- **Status:** {{ ucfirst($project_document->status) }}

@component('mail::button', ['url' => $url])
View Project Document
@endcomponent
 

Thanks,  
{{ config('app.name') }}
@endcomponent