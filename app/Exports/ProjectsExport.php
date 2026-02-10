<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectsExport  implements FromQuery, WithHeadings, WithMapping
// implements FromCollection 
{
    use Exportable;
    public $project_ids;
    public $sort_by;

    public function forCustomers(array $project_ids,string $sort_by)
    {
        $this->project_ids = $project_ids;
        $this->sort_by = $sort_by;

        return $this;
    }

    /**
     * Column headers
     */
    public function headings(): array
    {
        return [
            'Project Name',
            'Description',
            'Company / Agency',
            'Project Type',
            'Status',
            'Allow Resubmission',

            'Created By',
            'Updated By',
            'Created At',
            'Updated At',

            'Project Number',
            'RC Number',

            'Street',
            'Area',
            'Lot Number',

            // 'Submitter Response Duration Type',
            // 'Submitter Response Duration',
            // 'Submitter Due Date',

            // 'Reviewer Response Duration',
            // 'Reviewer Response Duration Type',
            // 'Reviewer Due Date',

            'Latitude',
            'Longitude',
            'Location',

            'Last Submitted At',
            'Last Submitted By',
            'Last Reviewed At',
            'Last Reviewed By',

            'Allotted Review Time (Hours)',

            // 'Staff Engineering Data',
            'Staff Initials',
            'Lot Size',
            'Unit of Size',
            'Site Area Inspection',
            'Burials Discovered Onsite',
            'Certificate of Approval',
            'Notice of Violation',

            'Installation',
            'Sub Area',
            'Project Size',
        ];
    }


    /**
     * Map each row
     */
    public function map($project): array
    {
        return [
            $project->name,
            $project->description,
            $project->agency, // shown as Company
            ucfirst($project->type), // local / federal

            strtoupper($project->status),

            $project->allow_project_submission ? 'Yes' : 'No',

            optional($project->creator)->name ?? $project->created_by,
            optional($project->updater)->name ?? $project->updated_by,

            optional($project->created_at)?->format('Y-m-d H:i'),
            optional($project->updated_at)?->format('Y-m-d H:i'),

            $project->project_number,
            $project->rc_number,

            $project->street,
            $project->area,
            $project->lot_number,

            // $project->submitter_response_duration_type,
            // $project->submitter_response_duration,
            // optional($project->submitter_due_date)?->format('Y-m-d'),

            // $project->reviewer_response_duration,
            // $project->reviewer_response_duration_type,
            // optional($project->reviewer_due_date)?->format('Y-m-d'),

            $project->latitude,
            $project->longitude,
            $project->location,

            optional($project->last_submitted_at)?->format('Y-m-d H:i'),
            optional($project->lastSubmittedBy)->name ?? $project->last_submitted_by,

            optional($project->last_reviewed_at)?->format('Y-m-d H:i'),
            optional($project->lastReviewedBy)->name ?? $project->last_reviewed_by,

            $project->allotted_review_time_hours,

            // $project->staff_engineering_data,
            $project->staff_initials,
            $project->lot_size,
            $project->unit_of_size,

            $project->site_area_inspection ? 'Yes' : 'No',
            $project->burials_discovered_onsite ? 'Yes' : 'No',
            $project->certificate_of_approval ? 'Yes' : 'No',
            $project->notice_of_violation ? 'Yes' : 'No',

            $project->installation,
            $project->sub_area,
            $project->project_size,
        ];
    }

    

    public function query()
    {

        $query = Project::query();
        switch ($this->sort_by) {
            case "Name A - Z":
                $query = $query->orderBy('projects.name', 'ASC');
                break;
            case "Name Z - A":
                $query = $query->orderBy('projects.name', 'DESC');
                break;
            case "Description A - Z":
                $query = $query->orderBy('projects.description', 'ASC');
                break;
            case "Description Z - A":
                $query = $query->orderBy('projects.description', 'DESC');
                break;
            case "Federal Agency A - Z":
                $query = $query->orderBy('projects.federal_agency', 'ASC');
                break;
            case "Federal Agency Z - A":
                $query = $query->orderBy('projects.federal_agency', 'DESC');
                break;
            case "Nearest Submission Due Date":
                $query = $query->withCount([
                    'project_reviewers as pending_submission_count' => fn($q) => $q->where('status', true)->where('review_status', 'rejected'),
                    'project_reviewers as not_fully_approved_count' => fn($q) => $q->where('status', true)->whereNot('review_status', 'approved')
                ])->orderByDesc('pending_submission_count')
                ->orderByDesc('not_fully_approved_count')
                ->orderBy('submitter_due_date', 'ASC');
                break;
            case "Farthest Submission Due Date":
                $query = $query->withCount([
                    'project_reviewers as pending_submission_count' => fn($q) => $q->where('status', true)->where('review_status', 'rejected'),
                    'project_reviewers as not_fully_approved_count' => fn($q) => $q->where('status', true)->whereNot('review_status', 'approved')
                ])->orderByDesc('pending_submission_count')
                ->orderByDesc('not_fully_approved_count')
                ->orderBy('submitter_due_date', 'DESC');
                break;
            case "Nearest Reviewer Due Date":
                $query = $query->withCount([
                    'project_reviewers as pending_review_count' => fn($q) => $q->where('status', true)->where('review_status', 'pending'),
                    'project_reviewers as not_fully_approved_count' => fn($q) => $q->where('status', true)->whereNot('review_status', 'approved')
                ])->orderByDesc('pending_review_count')
                ->orderByDesc('not_fully_approved_count')
                ->orderBy('reviewer_due_date', 'ASC');
                break;
            case "Farthest Reviewer Due Date":
                $query = $query->withCount([
                    'project_reviewers as pending_review_count' => fn($q) => $q->where('status', true)->where('review_status', 'pending'),
                    'project_reviewers as not_fully_approved_count' => fn($q) => $q->where('status', true)->whereNot('review_status', 'approved')
                ])->orderByDesc('pending_review_count')
                ->orderByDesc('not_fully_approved_count')
                ->orderBy('reviewer_due_date', 'DESC');
                break;
            case "Latest Added":
                $query = $query->orderBy('projects.created_at', 'DESC');
                break;
            case "Oldest Added":
                $query = $query->orderBy('projects.created_at', 'ASC');
                break;
            case "Latest Updated":
                $query = $query->orderBy('projects.updated_at', 'DESC');
                break;
            case "Oldest Updated":
                $query = $query->orderBy('projects.updated_at', 'ASC');
                break;
            default:
                // Default route-based sorting
                if (request()->routeIs('project.pending_project_update')) {
                    $query = $query->orderBy('projects.submitter_due_date', 'ASC');
                } elseif (request()->routeIs('project.in_review')) {
                    $query = $query->withCount([
                        'project_reviewers as pending_review_count' => fn($q) => $q->where('review_status', 'pending')
                    ])->orderByDesc('pending_review_count')
                    ->orderBy('reviewer_due_date', 'ASC');
                } else {
                    $query = $query->orderBy('projects.updated_at', 'DESC');
                }
                break;
        }

        return $query->whereIn('id', $this->project_ids);
    }
}
