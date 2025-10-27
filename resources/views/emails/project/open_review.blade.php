@component('mail::message')
# Open Project Review — {{ ucfirst($project_reviewer->reviewer_type ?? 'document') }} Review

A new project has been submitted for **Open Review**. You’re receiving this because you’re part of the
@php
    $team =   'Document Review Team';
@endphp
**{{ $team }}**.

@php
    // Map status to label & color (inline styles for broad email client support)
    $status = $project->status ?? 'submitted';
    $statusMap = [
        'submitted' => ['label' => 'Submitted', 'color' => '#1e90ff'],
        'in_review' => ['label' => 'In Review', 'color' => '#ff8c00'],
        'approved'  => ['label' => 'Approved 🎉', 'color' => '#228B22'],
        'rejected'  => ['label' => 'Rejected', 'color' => '#dc2626'],
        'completed' => ['label' => 'Completed', 'color' => '#6b21a8'],
        'cancelled' => ['label' => 'Cancelled', 'color' => '#6b7280'],
    ];
    $statusMeta = $statusMap[$status] ?? ['label' => ($project->status_text ?? ucfirst($status)), 'color' => '#111827'];
@endphp

## Project Details
- **Title:** {{ $project->name }}
- **Status:** <span style="display:inline-block;padding:.1rem .5rem;border-radius:.375rem;font-weight:700;background:#f8f9fa;color: {{ $statusMeta['color'] }};">{{ $statusMeta['label'] }}</span>

@switch($project_reviewer->reviewer_type)
    @case('document')
- **Document:** {{ $project_reviewer->project_document?->document_type?->name ?? 'N/A' }}
        @break
    @case('initial')
- **Review Type:** Initial Review
        @break
    @case('final')
- **Review Type:** Final Review
        @break
    @default
- **Review Type:** Document Review
@endswitch

@php
    $assignedName = $project_reviewer->user->name ?? null;
@endphp

@if(empty($assignedName))
> ✅ **Open Review Slot:** No reviewer is currently assigned.  
> The **first reviewer to open the project** will automatically become the **active reviewer**.
@else
- **Assigned To:** {{ $assignedName }}
@endif

- **Submitted by:** {{ $project->creator->name ?? 'Unknown' }}

@component('mail::panel')
{{ $project->description }}
@endcomponent

@component('mail::button', ['url' => $url])
Open Project for Review
@endcomponent

> ⚠️ **Heads-up:** This is an open review. If you’re first to access the project, you will be assigned as the active reviewer.

Thanks,  
{{ config('app.name') }}
@endcomponent
