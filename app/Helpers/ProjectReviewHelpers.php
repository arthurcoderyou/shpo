<?php 
namespace App\Helpers;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\ProjectReviewer;
 
class ProjectReviewHelpers
{


    // returns the reviewer name for the review
    public static function returnReviewerName(Review $review){
        // 2) Current reviewer (active/true, by order asc)
        $reviewer = null; 

        if($review->reviewer_id){

            $reviewer = User::find($review->reviewer_id);
            $reviewerName = $reviewer->name;

        }elseif($review->project_reviewer_id){
            $reviewer = ProjectReviewer::find($review->project_reviewer_id); 


            if(!empty( $reviewer)){

                // 3) Slot details
                $slotType = $reviewer->slot_type ?? null; // 'open' | 'person'
                $slotRole = $reviewer->slot_role ?? null; // only shown for 'person'

                // 4) Reviewer display name
                $reviewerName = 'Open review';
                if ($slotType === 'person') {
                    // assumes relation $reviewer->user
                    $reviewerName = optional($reviewer->user)->name ?: 'Unassigned person';
                }else{
                    if(!empty($reviewer->user_id)){
                        // assumes relation $reviewer->user
                        $reviewerName = optional($reviewer->user)->name ?: 'Unassigned person';
                    }

                }

            
            }else{
                $reviewerName = "Unnamed person";
            }

        }
        

        

        return $reviewerName;
    }



     // returns the slot data        : slot_type or slot_role        : the type can be slot_type or slot_role
    public static function returnSlotData(Review $review, $type = null){
         // 2) Current reviewer (active/true, by order asc)
        $reviewer = null;
        
        $reviewer = ProjectReviewer::find($review->project_reviewer_id); 
 
        // dd( $reviewer);


        // 3) Slot details
        $slotType = $reviewer->slot_type ?? null; // 'open' | 'person'
        $slotRole = $reviewer->slot_role ?? null; // only shown for 'person'


        if($type == "slot_type"){
            return $slotType;
        }else{
            return $slotRole;
        }
    }


}