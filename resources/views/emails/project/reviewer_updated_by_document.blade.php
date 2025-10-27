@component('mail::message')
# Project Reviewer List Updated

Hello {{ $user->name }},

The reviewer list for the project document **{{ $project_docuemnt->name }}** on **{{ $project->name }}** has been updated. Below is the new list of assigned reviewers by type.

@php
    // Helper to print a reviewer line (handles missing user as "Open Review")
    $printReviewer = function ($pr) {
        $order = $pr->order ?? '-';
        $name  = optional($pr->user)->name ?? 'Admin Review';
        $status = ucfirst($pr->review_status ?? 'pending');
        return "**{$order}.** {$name} || Review Status: {$status}";
    };

    
@endphp
 

{{-- DOCUMENT REVIEWERS (grouped per document type) --}}
## **{{ $project_docuemnt->name }} Reviewers **
 
@if(!empty($project_document))
{{-- ### {{ optional($project_document->document_type)->name ?? 'Untitled Document Type' }} --}}

@php
    $docReviewers = collect($project_document->project_reviewers ?? [])
        // ->where('reviewer_type', 'document')
        ->sortBy('order');
@endphp

@forelse ($docReviewers as $pr)
- {!! $printReviewer($pr) !!}
@empty
- _No document reviewers assigned for this document type._
@endforelse

@else
- _No project documents found._
@endif
 

@php
    $current_reviewer = $project_document->getCurrentReviewerByProjectDocument(); // expects a ProjectReviewer or null
@endphp

@if (!empty($current_reviewer))
@php 
    $currentDoc  = optional(optional($current_reviewer->project_document)->document_type)->name;
    $currentName = optional($current_reviewer->user)->name ?? 'Open Review';
@endphp

## **Current Reviewer**
- **{{ $current_reviewer->order ?? '-' }}.** {{ $currentName }} 
  @if($currentDoc) — **Document:** {{ $currentDoc }} @endif
  — **Status:** {{ ucfirst($current_reviewer->review_status ?? 'pending') }}
@endif


## **Project Details**
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
