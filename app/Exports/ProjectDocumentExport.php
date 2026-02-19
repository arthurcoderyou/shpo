<?php

namespace App\Exports;

use App\Models\ProjectDocument;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectDocumentExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /** @var array<int> */
    public array $project_document_ids = [];

    /** @var string|null */
    public ?string $sort_by = null; 

    public function forExportSorting(array $project_document_ids,string $sort_by)
    {
        $this->project_document_ids = $project_document_ids;
        $this->sort_by = $sort_by;

        return $this;
    }

    /**
     * Column headers
     */
    public function headings(): array
    {
        return [
            

            'Document ',
            'Document Status', 

            'Project Name',     
            'RC Number',
            'Company / Agency',
            'Project Type',
 
  

            'Last Submitted At',
            'Last Submitted By',

            // 'Submitter Response Duration Type',
            // 'Submitter Response Duration',
            // 'Submitter Due Date',

            // 'Reviewer Response Duration Type',
            // 'Reviewer Response Duration',
            // 'Reviewer Due Date',

            'Created By',
            'Updated By',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * Map each row
     */
    public function map($doc): array
    {
        // $doc is ProjectDocument
        $project = $doc->project;

        return [

            // Document type (prefers relation if you have it)
            $doc->document_type?->name ?? $doc->document_type_id,
            strtoupper(str_replace('_', ' ', (string) $doc->status)),

            $doc->project->name,    
            $doc->rc_number,
            $doc->project->type == "Private" ? optional($project->creator)->company  : $project->agency, // shown as Company
            ucfirst($project->type), // local / federal  
 
              
            optional($doc->last_submitted_at)?->format('Y-m-d H:i'),
            $doc->submitter?->name ?? $doc->last_submitted_by,

            // $doc->submitter_response_duration_type,
            // $doc->submitter_response_duration,
            // optional($doc->submitter_due_date)?->format('Y-m-d'),

            // $doc->reviewer_response_duration_type,
            // $doc->reviewer_response_duration,
            // optional($doc->reviewer_due_date)?->format('Y-m-d'),

            $doc->creator?->name ?? $doc->created_by,
            $doc->updator?->name ?? $doc->updated_by,

            optional($doc->created_at)?->format('Y-m-d H:i'),
            optional($doc->updated_at)?->format('Y-m-d H:i'),
        ];
    }

    public function query()
    {
        $query = ProjectDocument::query() ;


        switch ($this->sort_by) {
             

            // case "Description A - Z":
            //     return $q->withAggregate('project as project_description', 'description')
            //             ->orderBy('project_description', 'ASC');

            // case "Description Z - A":
            //     return $q->withAggregate('project as project_description', 'description')
            //             ->orderBy('project_description', 'DESC');

            // case "Federal Agency A - Z":
            //     return $q->withAggregate('project as project_agency', 'federal_agency')
            //             ->orderBy('project_agency', 'ASC');

            // case "Federal Agency Z - A":
            //     return $q->withAggregate('project as project_agency', 'federal_agency')
            //             ->orderBy('project_agency', 'DESC');

            // case "Nearest Submission Due Date":
            //     return $q->withAggregate('project as project_submitter_due', 'submitter_due_date')
            //             ->withCount([
            //                 'project_reviewers as pending_submission_count' => fn($r) => $r->where('status', true)->where('review_status', 'rejected'),
            //                 'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
            //             ])
            //             ->orderByDesc('pending_submission_count')
            //             ->orderByDesc('not_fully_approved_count')
            //             ->orderBy('project_submitter_due', 'ASC');

            // case "Farthest Submission Due Date":
            //     return $q->withAggregate('project as project_submitter_due', 'submitter_due_date')
            //             ->withCount([
            //                 'project_reviewers as pending_submission_count' => fn($r) => $r->where('status', true)->where('review_status', 'rejected'),
            //                 'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
            //             ])
            //             ->orderByDesc('pending_submission_count')
            //             ->orderByDesc('not_fully_approved_count')
            //             ->orderBy('project_submitter_due', 'DESC');

            // case "Nearest Reviewer Due Date":
            //     return $q->withAggregate('project as project_reviewer_due', 'reviewer_due_date')
            //             ->withCount([
            //                 'project_reviewers as pending_review_count' => fn($r) => $r->where('status', true)->where('review_status', 'pending'),
            //                 'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
            //             ])
            //             ->orderByDesc('pending_review_count')
            //             ->orderByDesc('not_fully_approved_count')
            //             ->orderBy('project_reviewer_due', 'ASC');

            // case "Farthest Reviewer Due Date":
            //     return $q->withAggregate('project as project_reviewer_due', 'reviewer_due_date')
            //             ->withCount([
            //                 'project_reviewers as pending_review_count' => fn($r) => $r->where('status', true)->where('review_status', 'pending'),
            //                 'project_reviewers as not_fully_approved_count' => fn($r) => $r->where('status', true)->whereNot('review_status', 'approved'),
            //             ])
            //             ->orderByDesc('pending_review_count')
            //             ->orderByDesc('not_fully_approved_count')
            //             ->orderBy('project_reviewer_due', 'DESC');

            case "Latest Added":
                $query = $query ->orderBy('created_at', 'DESC');
                break;
            case "Oldest Added":
                $query = $query ->orderBy('created_at', 'ASC');
                break;
            case "Latest Updated":
                $query = $query ->orderBy('updated_at', 'DESC');
                break;
            case "Oldest Updated":
                $query = $query->orderBy('updated_at', 'ASC');
                break;
            default:
                if (request()->routeIs('project.pending_project_update')) {
                    $query = $query->withAggregate('project as project_submitter_due', 'submitter_due_date')
                            ->orderBy('project_submitter_due', 'ASC');
                } elseif (request()->routeIs('project.in_review')) {
                    $query = $query->withAggregate('project as project_reviewer_due', 'reviewer_due_date')
                            ->withCount([
                                'project_reviewers as pending_review_count' => fn($r) => $r->where('review_status', 'pending')
                            ])
                            ->orderByDesc('pending_review_count')
                            ->orderBy('project_reviewer_due', 'ASC');
                }
                // $query = $query->withAggregate('project as project_updated_at', 'updated_at')
                        // ->orderBy('project_updated_at', 'DESC');

                $query = $query->orderBy('created_at', 'DESC');
                break;
        }

  
  
        return $query->whereIn('project_documents.id', $this->project_document_ids);
    }
}
