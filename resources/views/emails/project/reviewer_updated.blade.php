@component('mail::message')
# Project Reviewer List Updated

Hello {{ $user->name }},

The reviewer list for the project **{{ $project->name }}** has been updated. Below is the new list of assigned reviewers by type.

@php
    // Helper to print a reviewer line (handles missing user as "Open Review")
    $printReviewer = function ($pr) {
        $order = $pr->order ?? '-';
        $name  = optional($pr->user)->name ?? 'Open Review';
        $status = ucfirst($pr->review_status ?? 'pending');
        return "**{$order}.** {$name} || Review Status: {$status}";
    };

    // Project-level reviewers (Initial & Final) — sorted by order
    $projectReviewers = collect($project->project_reviewers ?? []);
    $initialReviewers = $projectReviewers->where('reviewer_type', 'initial')->sortBy('order');
    $finalReviewers   = $projectReviewers->where('reviewer_type', 'final')->sortBy('order');
@endphp

{{-- INITIAL REVIEWERS --}}
## **Initial Reviewers**
@forelse ($initialReviewers as $pr)
- {!! $printReviewer($pr) !!}
@empty
- _No initial reviewers assigned._
@endforelse


{{-- DOCUMENT REVIEWERS (grouped per document type) --}}
## **Document Reviewers (by Document Type)**
@php
    $projectDocuments = collect($project->project_documents ?? []);
@endphp

@forelse ($projectDocuments as $project_document)
### {{ optional($project_document->document_type)->name ?? 'Untitled Document Type' }}

@php
    $docReviewers = collect($project_document->project_reviewers ?? [])
        ->where('reviewer_type', 'document')
        ->sortBy('order');
@endphp

@forelse ($docReviewers as $pr)
- {!! $printReviewer($pr) !!}
@empty
- _No document reviewers assigned for this document type._
@endforelse

@empty
- _No project documents found._
@endforelse


{{-- FINAL REVIEWERS --}}
## **Final Reviewers**
@forelse ($finalReviewers as $pr)
- {!! $printReviewer($pr) !!}
@empty
- _No final reviewers assigned._
@endforelse


@php
    $current_reviewer = $project->getCurrentReviewer(); // expects a ProjectReviewer or null
@endphp

@if (!empty($current_reviewer))
@php
    $currentType = ucfirst($current_reviewer->reviewer_type ?? 'document');
    $currentDoc  = optional(optional($current_reviewer->project_document)->document_type)->name;
    $currentName = optional($current_reviewer->user)->name ?? 'Open Review';
@endphp

## **Current Reviewer**
- **{{ $current_reviewer->order ?? '-' }}.** {{ $currentName }}
  @if($currentType) — **Type:** {{ $currentType }} @endif
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
