<?php

namespace App\Livewire\Admin\Review;

use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ReviewAttachments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class ReviewList extends Component
{

    use WithFileUploads;
    use WithPagination;

    protected $listeners = [
        'projectReviewCreated' => '$refresh', 
        // 'reviewerUpdated' => '$refresh',
        // 'reviewerDeleted' => '$refresh',
        
    ];

    public $search = '';
    public $sort_by = '';
    public $record_count = 10;

    public $selected_records = [];
    public $selectAll = false;

    public $review_status;
    public $view_status;

    public $count = 0;

    public $file;

    public $project_id;
    public $project;

    public $project_search;
    public $next_reviewer;

    public function mount($id = null){


        if(request()->routeIs('review.index')){

            // $project_id = request()
            // $this->project_id = request()->query('project_id', ''); // Default to empty string if not set


            $this->project_id = request()->query('project_id', '');

            if(!empty( $this->project_id)){
                $project = Project::find($this->project_id);
                $this->project =  $project;
            }
            
            


        }else{
            $project = Project::find($id);
            $this->project =  $project;
            $this->project_id = $id;
        }
        

        if(!empty($project)){
            $this->next_reviewer = $project->getCurrentReviewer(); 

        }


    }


    public function mark_as_viewed($review_id){


        $review = Review::find($review_id);
        $review->viewed = true;
        $review->updated_at = now();
        $review->updated_by = Auth::user()->id;
        $review->save();


    }



    public function saerch_project($project_id){
         
            // dd($this->selected_records);
     
    
            // //create the query to pass
            // $queryParams = [];
            // $queryParams['reservation_ids'] = $reservation_ids;
    
            return redirect()->route('review.index',['project_id' => $project_id]);
     
            
    }

    

    // Method to delete selected records
    public function deleteSelected()
    {
        Review::whereIn('id', $this->selected_records)->delete(); // Delete the selected records


        $this->selected_records = []; // Clear selected records

        Alert::success('Success','Selected reviews deleted successfully');
        return redirect()->route('project.review',[]);
    }

    // This method is called automatically when selected_records is updated
    public function updateSelectedCount()
    {
        // Update the count when checkboxes are checked or unchecked
        $this->count = count($this->selected_records);
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selected_records = Review::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

    public function delete($id){
        $review = Review::find($id);




        // if(!empty($project->attachments)){

        //     foreach($project->attachments as $attachment){ 
 
        //         // Construct the full file path
        //         $filePath = "public/uploads/project_attachments/{$attachment->attachment}";

        //         // Check if the file exists in storage and delete it
        //         if (Storage::exists($filePath)) {
        //             Storage::delete($filePath);
        //         }

        //         // Delete the record from the database
        //         $attachment->delete();
        //     }

            

        // }



        $review->delete();

 

        Alert::success('Success','Project deleted successfully');
        return redirect()->route('project.review',['project' => $this->project_id]);

    }


    public function removeUploadedAttachment(int $id){

        // dd($id, gettype($id)); // Check the actual value and type
        // dd($id);
        // Find the attachment record
        $attachment = ReviewAttachments::find($id);

        if (!$attachment) {
            session()->flash('error', 'Attachment not found.');
            return;
        }

        // Construct the full file path
        $filePath = "public/uploads/review_attachments/{$attachment->attachment}";

        // Check if the file exists in storage and delete it
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        // Delete the record from the database
        $attachment->delete();


        Alert::success('Success','Review attachment deleted successfully');
        return redirect()->route('project.review',['project' => $attachment->project_id]);


    }



    public function generatePdf(){

        // dd("PDF");
        // dd($this->selected_records );


 
        $data = [];

        foreach ($this->selected_records as $id) {
            // Fetch the player registration with related details
            $review = Review::where('id', $id)->first();

            if ($review) {
               // Grouping registered attachments 
                $review_attachments = [];

                foreach ($review->attachments as $attachment) {
                    if (!empty($attachment->attachment)) {
                        $review_attachments[] = $attachment->attachment;
                    }
                }
                
                $next_reviewer = '';
                if(!empty($review->project)){
                    $next_reviewer = $review->project->getCurrentReviewer();  
                }


                $data[] = [
                    'id' => $review->id,
                    'project' => $review->project->name,
                    'project_status' => $review->project->getStatusTextAttribute(),
                    'review_status' => $review->review_status,
                    'review_created_at' => $review->created_at, 
                    'project_review' => $review->project_review,

                    'next_reviewer' => !empty($next_reviewer->user) ? $next_reviewer->user->name : '',

                    'reviewer_review' => !$review->isSubmitterReview() && $review->review_status !== "submitted", // if this is true, then reviewer view should be used
                    'reviewer_due_date' => $review->project->reviewer_due_date ?  $review->project->reviewer_due_date : null,


                    'admin_review' => $review->admin_review,
                    'reviewer' => [
                        'name' => $review->reviewer->name ?? 'N/A', 
                        'email' => $review->reviewer->email ?? 'N/A',  
                    ],
                    'review_attachments' => $review_attachments, // Grouped and structured attachments  
                ];
            }
        }


 
 
        $data_array = [
            'data' => $data
        ];
 
        // dd($data);
        // Generate PDF using a view
        // $pdf = Pdf::loadView('report.users', $data);
        $pdf = Pdf::loadView('report.report', $data_array);

        $pdf_title = 'SHPO_Project_Review _Report_'.date("d_m_Y h_i_A",strtotime(now())).'.pdf';

        // Set metadata for the PDF document
        $options = $pdf->getDomPDF()->getOptions();
        $options->set('isHtml5ParserEnabled', true); // Optional: Enable HTML5 parsing
        $options->set('isRemoteEnabled', true);      // Optional: Enable loading of external resources

        // Set the document title, author, and other metadata
        $pdf->getDomPDF()->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {

            $pdf_title = 'SHPO_Project_Review _Report_'.date("d_m_Y h_i_A",strtotime(now())).'.pdf';
            // Set metadata using the correct format
            $canvas->get_cpdf()->metadata['Title'] = $pdf_title;
            $canvas->get_cpdf()->metadata['Author'] = Auth::user()->name;
            $canvas->get_cpdf()->metadata['Subject'] = 'SHPO_Project_Review _Report_for_'.date("d_m_Y h_i_A",strtotime(now()));
            $canvas->get_cpdf()->metadata['Keywords'] = 'SHPO_Project_Review _Report_for_'.date("d_m_Y h_i_A",strtotime(now()));
        });




        // // // // Return the PDF as a download or inline view
        // return response()->streamDownload(function () use ($pdf) {
        //     print $pdf->stream();

        // }, $pdf_title);

        // Define the directory and file path
        $storagePath = storage_path('app/public/reports/');
        $filePath = $storagePath . $pdf_title;

        // Check if the directory exists, if not, create it
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);  // Creates the directory with correct permissions
        }

        // Save the PDF file to the storage (in 'storage/app/public/reports/')
        $pdf->save($filePath);

        // Get the public URL to the file (using the Laravel storage link)
        $fileUrl = asset('storage/reports/' . $pdf_title);

        // Return the URL as JSON to the front-end
        // return redirect($fileUrl);
        // Redirect to the PDF URL in a new tab
        // return redirect()->to($fileUrl);
        // session()->flash('fileUrl', $fileUrl);

        session()->put('pdf_url', $fileUrl);

        $this->fileUrl = $fileUrl;
        return redirect('/review');

       // Dispatch browser event to open the PDF in a new tab
        //    $this->dispatch('open-pdf', ['url' => $fileUrl]);



        // Emit the file URL to the frontend to open in a new tab
        // $this->dispatchBrowserEvent('openPdfInNewTab', ['url' => $fileUrl]);


    }




    
    public function render()
    {

        $reviews = Review::select('reviews.*');

        // if(Auth::user()->hasRole('Admin')){
        //     if(!empty( $this->project_id)){
        //         $reviews = $reviews->where('project_id' ,$this->project_id);
        //     }

        // }else{
        //     $reviews = $reviews->where('project_id' ,$this->project_id);
        // }
            

            // if (!empty($this->search)) {
            //     $search = $this->search;
            //     // dd($this->search);

            //     $reviews = $reviews
            //         ->join('users', 'users.id', '=', 'reviews.reviewer_id')
            //         ->where(function ($query) use ($search) {
            //             $query->where('reviews.project_review', 'LIKE', '%' . $search . '%')
            //                 ->orWhere('users.name', 'LIKE', '%' . $search . '%');
            //         })
            //         ->select('reviews.*'); // Ensure you select the correct columns


            // }
            if (!empty($this->search)) {
                $reviews = $reviews->orWhere(function ($query) {
                    $query->whereHas('reviewer', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%");
                    });
                });


                $reviews = $reviews->orWhere('project_review' ,'LIKE','%'.$this->search.'%' );

 

            }
            if (!empty($this->review_status)) {
                $reviews = $reviews->where('review_status' ,$this->review_status );
            }

            if (!empty($this->view_status)) {

                if($this->view_status == "viewed"){
                    $reviews = $reviews->where('viewed' ,true );
                }elseif($this->view_status == "not_viewed"){
                    $reviews = $reviews->where('viewed' ,false );
                }else{

                }

                
            }
            

            // // Find the role
            // $role = Role::where('name', 'DSI God Admin')->first();

            // if ($role) {
            //     // Get user IDs only if role exists
            //     $dsiGodAdminUserIds = $role->reviews()->pluck('id');
            // } else {
            //     // Set empty array if role doesn't exist
            //     $dsiGodAdminUserIds = [];
            // }


            // // if(!Auth::user()->hasRole('DSI God Admin')){
            // //     $reviews =  $reviews->where('reviews.created_by','=',Auth::user()->id);
            // // }

            // // Adjust the query
            // if (!Auth::user()->hasRole('DSI God Admin') && !Auth::user()->hasRole('Admin')) {

            //     $reviews = $reviews->where('reviews.created_by', '=', Auth::user()->id);

            // }else
            
           
            // $reviews = $reviews->whereNotIn('reviews.created_by', $dsiGodAdminUserIds);
            // if(Auth::user()->hasRole('User')){
            //     $reviews = $reviews->where('reviews.created_by', '=', Auth::user()->id);
            // }
         


        // dd($this->sort_by);
        if(!empty($this->sort_by) && $this->sort_by != ""){
            // dd($this->sort_by);
            switch($this->sort_by){
 
                case "Description A - Z":
                    $reviews =  $reviews->orderBy('reviews.description','ASC');
                    break;
 
                case "Description Z - A":
                    $reviews =  $reviews->orderBy('reviews.description','DESC');
                    break;

                case "Federal Agency A - Z":
                    $reviews =  $reviews->orderBy('reviews.federal_agency','ASC');
                    break;
    
                case "Federal Agency Z - A":
                    $reviews =  $reviews->orderBy('reviews.federal_agency','DESC');
                    break;
    

                /**
                 * "Latest" corresponds to sorting by created_at in descending (DESC) order, so the most recent records come first.
                 * "Oldest" corresponds to sorting by created_at in ascending (ASC) order, so the earliest records come first.
                 */

                case "Latest Added":
                    $reviews =  $reviews->orderBy('reviews.created_at','DESC');
                    break;

                case "Oldest Added":
                    $reviews =  $reviews->orderBy('reviews.created_at','ASC');
                    break;

                case "Latest Updated":
                    $reviews =  $reviews->orderBy('reviews.updated_at','DESC');
                    break;

                case "Oldest Updated":
                    $reviews =  $reviews->orderBy('reviews.updated_at','ASC');
                    break;
                default:
                    $reviews =  $reviews->orderBy('reviews.updated_at','DESC');
                    break;

            }


        }else{
            $reviews =  $reviews->orderBy('reviews.created_at','DESC');

        }




        if(!empty( $this->project_id)){
            $reviews = $reviews->where('project_id' ,$this->project_id);
        }


        $this->selected_records = $reviews->pluck('id')->toArray();


        $reviews = $reviews->paginate($this->record_count);


        // $this->project_search 
        
        $results = Project::select('projects.*');
        if (!empty($this->project_search) && strlen($this->project_search) > 0) {
            $search = $this->project_search;

            // $results = $results->where(function ($query) use ($search) {
            //     $query->where('projects.name', 'LIKE', '%' . $search . '%')
            //     ->where('projects.name', 'LIKE', '%' . $search . '%')
            //         ;
            // });


            $results = $results->where(function($query) use ($search) {
                $query->where('projects.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.federal_agency', 'LIKE', '%' . $search . '%')
                    ->orWhere('projects.description', 'LIKE', '%' . $search . '%')
                    // ->orWhereHas('creator', function ($query) use ($search) {
                    //     $query->where('users.name', 'LIKE', '%' . $search . '%')
                    //         ->where('users.email', 'LIKE', '%' . $search . '%');
                    // })
                    // ->orWhereHas('updator', function ($query) use ($search) {
                    //     $query->where('users.name', 'LIKE', '%' . $search . '%')
                    //         ->where('users.email', 'LIKE', '%' . $search . '%');
                    // })
                    ->orWhereHas('project_reviewers.user', function ($query) use ($search) {
                        $query->where('users.name', 'LIKE', '%' . $search . '%')
                        ->where('users.email', 'LIKE', '%' . $search . '%');
                    });
            });


        }
        $results =  $results->limit(10)->get();




        return view('livewire.admin.review.review-list',[
            'reviews' => $reviews,
            'results' => $results
        
        ]);
    }
}
