Project Reviewer List Updated
=============================

Hello {{ $user->name }},

The reviewer list for the project document "{{ $document->document_type->name }}" 
on project "{{ $project->name }}" has been updated. 
Below is the new list of assigned reviewers by type.

------------------------------------------------------------
Project: {{ $project->name ?? '—' }}
Document: {{ $document->document_type->name ?? '—' }}
------------------------------------------------------------

{{ strtoupper($document->document_type->name) }} REVIEWERS
------------------------------------------------------------

@php
    $docReviewers = collect($document->project_reviewers ?? [])->sortBy('order');
@endphp

@forelse ($docReviewers as $pr)
@php
    $order  = $pr->order ?? '-';
    $name   = optional($pr->user)->name ?? 'Open Review';
    $status = ucfirst($pr->review_status ?? 'pending');
@endphp
{{ $order }}. {{ $pr->slot_type == 'person' ? $name : 'Open Review' }} 
   - Review Status: {{ $status }}
@empty
No reviewers assigned for this document.
@endforelse


@php
    $current_reviewer = $document->getCurrentReviewerByProjectDocument();
@endphp

@if (!empty($current_reviewer))
@php
    $currentDoc  = optional(optional($current_reviewer->project_document)->document_type)->name;
    $currentName = optional($current_reviewer->user)->name ?? 'Open Review';
    $currentStat = ucfirst($current_reviewer->review_status ?? 'pending');
@endphp

------------------------------------------------------------
Current Reviewer
------------------------------------------------------------
{{ $current_reviewer->order ?? '-' }}. {{ $currentName }}
@if($currentDoc)
   - Document: {{ $currentDoc }}
@endif
   - Status: {{ $currentStat }}
@endif


------------------------------------------------------------
View the full project details here:
{{ $viewUrl }}
------------------------------------------------------------

Please review the project as necessary.

Thanks,
{{ config('app.name') }}
