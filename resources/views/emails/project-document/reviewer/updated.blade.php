@component('mail::message')
# Project Reviewer List Updated

Hello {{ $user->name }},

The reviewer list for the project document **{{ $document->document_type->name }}** on **{{ $project->name }}** has been updated. Below is the new list of assigned reviewers by type.

@php
    // Helper to print a reviewer line (handles missing user as "Open Review")
    $printReviewer = function ($pr) {
        $order = $pr->order ?? '-';
        $name  = optional($pr->user)->name ?? 'Open Review';
        $status = ucfirst($pr->review_status ?? 'pending');
        return "**{$order}.** {$name} || Review Status: {$status}";
    };

    
@endphp
 
@component('mail::panel')
**Project:** {{ $project->name ?? '—' }}  
**Document:** {{ $document->document_type->name ?? '—' }}    
@endcomponent

{{-- DOCUMENT REVIEWERS (grouped per document type) --}}
## ** {{ $document->document_type->name }} Reviewers **
 
@if(!empty($document))
{{-- ### {{ optional($project_document->document_type)->name ?? 'Untitled Document Type' }} --}}

@php
    $docReviewers = collect($document->project_reviewers ?? [])
        // ->where('reviewer_type', 'document')
        ->sortBy('order');
@endphp

@forelse ($docReviewers as $pr)
- {!! $pr->slot_type == "person" ? $printReviewer($pr) : "**{$pr->order}.** Open Review || Review Status: {ucfirst($pr->review_status ?? 'pending')}" !!}
@empty
- _No reviewers assigned for this document._
@endforelse

@else
- _No project documents found._
@endif
 

@php
    $current_reviewer = $document->getCurrentReviewerByProjectDocument(); // expects a ProjectReviewer or null
@endphp

@if (!empty($current_reviewer))
@php 
    $currentDoc  = optional(optional($current_reviewer->project_document)->document_type)->name;
    $currentName = optional($current_reviewer->user)->name ?? 'Open Review';
@endphp

## **Current Reviewer**
- **{{ $current_reviewer->order ?? '-' }}.** {{ $currentName }} — **Status:** {{ ucfirst($current_reviewer->review_status ?? 'pending') }}
@endif




@component('mail::button', ['url' => $viewUrl])
View Project Document
@endcomponent

Please review the project as necessary.

Thanks,  
{{ config('app.name') }}
@endcomponent
