<?php

namespace App\Http\Controllers;

use App\Events\ProjectDocument\Review\ReviewSubmitted;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Events\ProjectDocumentSubmitted;
use App\Events\ProjectDocument\Submitted;
use App\Events\ProjectDocument\Review\Reviewed;
use App\Events\ProjectDocument\Review\ReviewRequest;
use App\Events\ProjectDocument\ProjectReviewer\Updated;
use App\Events\ProjectDocument\Review\OpenReviewRequest;
use App\Events\ProjectDocument\Review\FollowupReviewRequest;

class TestingController extends Controller
{

    /** @abstract
     *  Notes
     * This is connected to admin/test/test.blade.php
     * All created user interface design will be tested there 
     * 
     * 
     */


    // test/project
    public function test_project(){ 

        $test_route = "test/project";
        $page_label = "Project List";

        return view('admin.test.test',[
            'test_route' => $test_route,
            'page_label' => $page_label,
        ]);
    }




     // test/project_document
    public function test_project_document(){
            
        $test_route = "test/project_document";
        $page_label = "Project Document";

        return view('admin.test.test',[
            'test_route' => $test_route,
            'page_label' => $page_label,
        ]);
         
    }



    
    // test/project_document_review 
    public function test_project_document_review(){

        
        $test_route = "test/project_document_review";
        $page_label = "Project Document Review";

        return view('admin.test.test',[
            'test_route' => $test_route,
            'page_label' => $page_label,
        ]);
    }



     
    // test/project/table 
    public function test_project_table(){

        
        $test_route = "test/project/table";
        $page_label = "Project Table";

        return view('admin.test.test',[
            'test_route' => $test_route,
            'page_label' => $page_label,
        ]);
    }

    // test/project/show 
    public function test_project_show(){

        
        $test_route = "test/project/show";
        $page_label = "Project show";

        return view('admin.test.test',[
            'test_route' => $test_route,
            'page_label' => $page_label,
        ]);
    }

    // test/project/show_2
    public function test_project_show_2(){

        
        $test_route = "test/project/show_2";
        $page_label = "Project show";

        return view('admin.test.test',[
            'test_route' => $test_route,
            'page_label' => $page_label,
        ]);
    }

    // test/review/list
    public function test_review_list(){

        
        $test_route = "test/review/list";
        $page_label = "Project Document Review List";

        return view('admin.test.test',[
            'test_route' => $test_route,
            'page_label' => $page_label,
        ]);
    }




    // test the events
    public function testEventProjectDocumentSubmitted(){

        // ProjectDocumentSubmitted TEST
        event(new ProjectDocumentSubmitted(217, 73));
        return 'ProjectDocumentSubmitted fired!';
        // ./ ProjectDocumentSubmitted TEST

    }




    public function testEventSubmitted(){


        // user cerbs is id 73

        // Submitted TEST
        event(new Submitted(217, 73, true, true));
        return 'Submitted fired!';
        // ./ Submitted TEST

    }


    public function testReviewRequest(){

        // project id is 223
        // user id of milane is 71 
        // project reviewer id is 766
 
        event(new ReviewRequest(766, 73, true, true));
        return 'ReviewRequest fired!';
 
    }
 
    public function testOpenReviewRequest(){

        // project id is 223
        // user id of quirion is 32 
        // project reviewer id for open review is 728
 
        event(new OpenReviewRequest(728,32, 73, true, true));
        return 'OpenReviewRequest fired!';
 
    }



    public function testFollowupReviewRequest(){

        // 
        // project id is 223
        // user id of milane is 71 
        // project reviewer id is 766
 
        event(new FollowupReviewRequest(766, 73, true, true));
        return 'testFollowupReviewRequest fired!';
 
    }


     public function testReviewerListUpdated(){

        // project id is 210
        // project document id is 203
       
        // user id of milane is 71  . we want to notify milane
        // authId user cerbs is id 73  



        
 
        event(new Updated(203,71, 73, true, true));
        return 'Updated fired!';
 
    }



    public function testReviewedNotification(){


        // Sample: Cadman Warner Project
        // project id is 238
        // project document id is 230       HAbb Hear
       
        // user id of milane is 71  . we want to notify milane
        // authId user cerbs is id 73  

        // review id is 422
        
        $review = Review::find(422);
        // dd($review);
        // dd($review->project_reviewer_id);
 
        event(new Reviewed(422,71, 73, true, true));
        return 'Reviewed fired!';
 
    }


    public function testReviewSubmittedNotification(){


        // Sample: Cadman Warner Project
        // project id is 238
        // project document id is 230       HAbb Hear
       
        // user id of ArthurBonsilaoCervania is 39  . we want to notify ArthurBonsilaoCervania
        // authId user cerbs is id 73  

        // review id is 422
        
        $review = Review::find(422);
        // dd($review);
        // dd($review->project_reviewer_id);
 
        event(new ReviewSubmitted(422,39, 73, true, true));
        return 'ReviewSubmitted fired!';
 
    }


 
}
