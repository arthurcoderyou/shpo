<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Review;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReviewExport implements FromQuery, WithHeadings, WithMapping

// implements FromCollection
{
    /** @var array<int> */
    public array $review_ids = [];

    /** @var string|null */
    public ?string $sort_by = null; 

    public function forExportSorting(array $review_ids,string $sort_by)
    {
        $this->review_ids = $review_ids;
        $this->sort_by = $sort_by;

        return $this;
    }

    /**
     * Column headers
     */
    public function headings(): array
    {
        return [


             

            'Reviewed Document ', 

            'Reviewed Project',     
            'RC Number', 
 
            
            'Reviewer',
            'Review',
            'Review Status',
 
            // 'Submitter Response Duration Type',
            // 'Submitter Response Duration',
            // 'Submitter Due Date',

            // 'Reviewer Response Duration Type',
            // 'Reviewer Response Duration',
            // 'Reviewer Due Date',

            'Created By', 
            'Created At', 
        ];
    }

    /**
     * Map each row
     */
    public function map($review): array
    {
        // $doc is ProjectDocument
        $project = $review->project;
        $document = $review->project_document;

        return [

            $document->document_type?->name,    

            $project?->name,    
            $project?->rc_number,

            $review->reviewer?->name,
            $review->project_review, 
            strtoupper(str_replace('_', ' ', (string) $review->review_status)),
              
           

            // $doc->submitter_response_duration_type,
            // $doc->submitter_response_duration,
            // optional($doc->submitter_due_date)?->format('Y-m-d'),

            // $doc->reviewer_response_duration_type,
            // $doc->reviewer_response_duration,
            // optional($doc->reviewer_due_date)?->format('Y-m-d'),

            $review->creator?->name ?? $review->created_by, 

            optional($review->created_at)?->format('Y-m-d H:i'), 
        ];
    }

    public function query()
    {
        $query = Review::select('reviews.*');

         

        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){
 
                case "Description A - Z":
                    $query =  $query->orderBy('reviews.description','ASC');
                    break;
 
                case "Description Z - A":
                    $query =  $query->orderBy('reviews.description','DESC');
                    break;

                case "Federal Agency A - Z":
                    $query =  $query->orderBy('reviews.federal_agency','ASC');
                    break;
    
                case "Federal Agency Z - A":
                    $query =  $query->orderBy('reviews.federal_agency','DESC');
                    break;
    

                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $query =  $query->orderBy('reviews.created_at','DESC');
                    break;

                case "Oldest Added":
                    $query =  $query->orderBy('reviews.created_at','ASC');
                    break;

                case "Latest Updated":
                    $query =  $query->orderBy('reviews.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $query =  $query->orderBy('reviews.updated_at','ASC');
                    break;
                default:
                    $query =  $query->orderBy('reviews.updated_at','DESC');
                    break;

            }


        }else{
            $query =  $query->orderBy('reviews.created_at','DESC');

        }


  
  
        return $query->whereIn('reviews.id', $this->review_ids);
    }
}
