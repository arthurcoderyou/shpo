@component('mail::message')
# Open Project Review Notification

A new project has been submitted for **Open Review**. You are receiving this notification because you are part of the review team.

The first reviewer to access the project will be automatically assigned as the active reviewer.

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
@switch($project_reviewer->reviewer_type)
    @case("document")
        - **Document:** {{ $project_reviewer->project_document->document_type->name ?? 'N/A' }}
        @break
    @case("initial")
        - **Initial Review**  
        @break
    @case("final")
        - **Final Review** 
        @break
    @default    
        - **Review** 
@endswitch 


- **Submitted by:** {{ $project->creator->name }}

@component('mail::panel')
{{ $project->description }}
@endcomponent

@component('mail::button', ['url' => $url])
Open Project for Review
@endcomponent

> ⚠️ This project requires review. The first admin to access it will be assigned as the reviewer.

Thanks,  
{{ config('app.name') }}
@endcomponent
