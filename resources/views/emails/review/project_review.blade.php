@component('mail::message')
# Project Review Completed  

Hello {{ $project->creator->name }},  

Your project **{{ $project->name }}** has been successfully reviewed. Please see the details of the review below.  

## Review Summary
- **Project Title:** {{ $project->name }}  
- **Review Status:** {{ ucfirst($review->review_status) }}  
- **Reviewed On:** {{ $review->created_at->format('F j, Y \a\t g:i A') }}  

@component('mail::panel')
{{ $review->project_review }}
@endcomponent  

@component('mail::button', ['url' => $url])
View Review Details
@endcomponent  

We appreciate your continued effort in ensuring the quality and progress of this project.  

Thank you,  
{{ config('app.name') }}
@endcomponent
