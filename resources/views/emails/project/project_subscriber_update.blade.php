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
# Project Update Notification

Hello {{ $user->name }},

@switch($message_type)
    @case('project_submitted')
        @php $message = "Project <strong>{$project->name}</strong>, has been submitted for review. Stay tuned for updates."; @endphp
        @break

    @case('project_approved')
        @php $message = "Project <strong>{$project->name}</strong>, had been completed the approval process"; @endphp
        @break

    @case('project_reviewed')
        @php $message = "The project <strong>{$project->name}</strong> has been reviewed. Check out the latest project review."; @endphp
        @break

    @case('project_resubmitted')
        @php $message = "The project <strong>{$project->name}</strong> has been updated and resubmitted for review."; @endphp
        @break

    @case('project_reviewers_updated')
        @php $message = "The list of reviewers for the project <strong>{$project->name}</strong> has been updated."; @endphp
        @break

    @default
        @php $message = "There is an important update regarding the project <strong>{$project->name}</strong>."; @endphp
@endswitch


@component('mail::panel')
{!! $message !!}
@endcomponent



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

{{-- - **Submitted by:** {{ $project->creator->name }}   --}}
- **Last Updated:** {{ $project->updated_at->format('F j, Y, g:i A') }}



@component('mail::button', ['url' => $url])
View Project
@endcomponent
 

Thanks,  
{{ config('app.name') }}
@endcomponent