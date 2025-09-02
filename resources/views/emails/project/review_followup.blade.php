@component('mail::message')
# Project Review Follow-Up

Hello {{ $reviewer->user->name }},

You are assigned as the **{{ ucfirst($reviewer->reviewer_type) }} Reviewer** for the project **{{ $project->name }}**.  
The project has been updated by the creator, and your review is required again to assess the latest changes.

## Updated Project Details:
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

@if($reviewer->reviewer_type === 'document')
- **Document to Review:** {{ $reviewer->project_document->document_type->name ?? 'N/A' }}
@endif

- **Submitted by:** {{ $project->creator->name }}
- **Last Updated:** {{ optional($project->updated_at)->format('F j, Y, g:i A') }}

@component('mail::panel')
{{ $project->description }}
@endcomponent

@component('mail::button', ['url' => $url])
Review Updated Project
@endcomponent

Your prompt review as the **{{ ucfirst($reviewer->reviewer_type) }} Reviewer** is appreciated to ensure a smooth approval process.

Thanks,  
{{ config('app.name') }}
@endcomponent
