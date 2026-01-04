<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use App\Models\ProjectTimer;
use Illuminate\Http\Request;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\User\NewUserVerifiedMail;
use App\Helpers\ProjectReviewerHelpers;
use App\Events\ProjectDocumentSubmitted;
use App\Events\ProjectDocument\Submitted;
use App\Mail\ProjectDocument\SubmittedMail;
use App\Events\ProjectDocument\Review\Reviewed;
use App\Events\ProjectTimer\ProjectTimerLogEvent;
use App\Helpers\ActivityLogHelpers\UserLogHelper;
use App\Mail\User\NewUserVerificationRequestMail;
use App\Mail\ProjectTimer\TimeSettingsUpdatedMail;
use App\Events\ProjectDocument\Review\ReviewRequest;
 
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Events\ProjectDocument\Review\ReviewSubmitted;
use App\Events\ProjectDocument\ProjectReviewer\Updated;
use App\Events\ProjectDocument\Review\OpenReviewRequest;
use App\Helpers\ActivityLogHelpers\ProjectTimerLogHelper;
use App\Events\ProjectDocument\Review\FollowupReviewRequest;


class TestingController extends Controller
{


     //====================//
    //  test the views
    //====================//

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

    // test/review/re_review
    public function re_review(){


        $test_route = "test/review/re_review";
        $page_label = "Re-Review ";

        return view('admin.test.test',[
            'test_route' => $test_route,
            'page_label' => $page_label,
        ]);


    }

    // test/review/digital_signature
    public function digital_signature(){


        $test_route = "test/review/digital_signature";
        $page_label = "Project Document Review Digital Signature";

        return view('admin.test.test',[
            'test_route' => $test_route,
            'page_label' => $page_label,
        ]);


    }

    // test/review/review_list_layout
    public function review_list_layout(){
        $test_route = "test/review/review_list_layout";
        $page_label = "Review List Layout";

        return view('admin.test.test',[
            'test_route' => $test_route,
            'page_label' => $page_label,
        ]);
    }



    //====================//
    //  test the events
    //====================//

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


    public function textProjectEventSubmitted(){

        // project_id = 302;
        // Cerbs = user_id = 73

        $project = Project::find( 302);
        
        $user = User::find(73);

        // dd($user);
        // dd($project);

        $viewUrl = ProjectLogHelper::getRoute('submitted',$project->id,73);

        // dd($viewUrl);

        event(new \App\Events\Project\Submitted($project->id,73, true, true));
        return  new \App\Mail\Project\SubmittedMail($project,$viewUrl) ;

    }


    public function testProjectRCReviewed(){

        // project_id = 302;
        // Cerbs = user_id = 73

        $project = Project::find( 302);
        
        $user = User::find(73);

        // dd($user);
        // dd($project);

        $viewUrl = ProjectLogHelper::getRoute('rc-reviewed',$project->id,73);

        // dd($viewUrl);

        event(new \App\Events\Project\Reviewed($project->id,73, true, true));
        return  new \App\Mail\Project\ReviewedMail($project,$viewUrl) ;

    }


     public function textProjectEventReviewRequest(){

        // project_id = 275;
        // Mark = user_id = 74

        $project = Project::find( 270);

        $project_reviewer = $project->project_reviewers->first();

        // dd($project_reviewer->user->name);

        // <a href="http://127.0.0.1:8000/project/270/project_document/268/show" wire:navigate="" class="rounded-md bg-black px-2.5 py-1 text-xs font-medium text-white">
        //                                                 View
        //                                             </a>

        event(new \App\Events\Project\Review\ReviewRequest($project_reviewer->id,  $project_reviewer->user_id, true, true));
        return 'Project review request submitted fired!';

    }


     public function textProjectEventOpenReviewRequest(){

        // project_id = 275;
        // Mark = user_id = 74

        $project = Project::find( 270);

        // $project_reviewer = $project->project_reviewers->first();


        // project reviewer id 658

        $current_reviewer = ProjectReviewer::find(658);

        // dd($current_reviewer);

        // dd($project_reviewer->user->name);

        // <a href="http://127.0.0.1:8000/project/270/project_document/268/show" wire:navigate="" class="rounded-md bg-black px-2.5 py-1 text-xs font-medium text-white">
        //                                                 View
        //                                             </a>

        $auth_user_id = 74;


        $auth_user_id = 74;

        // Get admin IDs (users with system access admin)
        $adminIds = User::permission('system access admin')
            ->pluck('id')
            ->filter() // remove null
            ->map(fn ($id) => (int) $id)
            ->reject(fn ($id) => $id === (int) $auth_user_id) // remove actor
            ->unique()
            ->values();

        // dd($adminIds);
 

            if ($adminIds->isEmpty()) {
            // dd("Here");
                Log::info('No admin recipients for OpenReviewRequest.', [
                    'project_reviewer_id' => $current_reviewer->id,
                    'project_document_id' => $current_reviewer->project_document_id,
                ]);
            } else {
                // dd("Array Here");
                foreach ($adminIds as $userId) {
                    try {
                        event(new \App\Events\Project\Review\OpenReviewRequest(
                            $current_reviewer->id, // project_reviewer_id
                            $userId,               // recipient user id
                            $auth_user_id,
                            true,                  // send email
                            true                   // send broadcast
                        ));
                    } catch (\Throwable $e) {
                        Log::error('Failed to dispatch OpenReviewRequest event.', [
                            'error'                 => $e->getMessage(),
                            'project_reviewer_id'   => $current_reviewer->id,
                            'recipient_user_id'     => $userId,
                            'actor_user_id'         => Auth::id(),
                            'trace'                 => $e->getTraceAsString(),
                        ]);
                    }
                }
            }








        return 'Project open review request submitted fired!';

    } 


    public function textProjectTimerUpdateEvent(){ 

        /*
        // test the mail    
        $projectTimer = ProjectTimer::first();
        $targetUser = User::first();

        return new  TimeSettingsUpdatedMail(
                        $projectTimer,
                        $targetUser,
                        route('dashboard'),
                        'guest', // change to admin, reviewer, user ,guest to test
                        
                    );
        */



        /*
        // test the event 
        $projectTimer = ProjectTimer::first();
        $authId = $projectTimer->updated_by;
        $targetUser = User::first();

        $updatedBy = User::find($authId);
        
 
        event(new \App\Events\ProjectTimer\TimeSettingsUpdated(
            projectTimerId: $projectTimer->id, 
            targetUserId: $targetUser->id,
            targetUserEmailForRole: 'admin',
            authId: $authId, 
            sendMail: true
        ));
        return 'Project timer updated for'. $targetUser->name.' on email:'.$targetUser->email.' || Project timer updated_by '.$updatedBy->name;


        */


 
        $authId = Auth::check() ? Auth::id() : null;

        // get the message from the helper 
        $message = ProjectTimerLogHelper::getActivityMessage('default', $authId);

        // get route 
        $route = ProjectTimerLogHelper::getRoute('default');

        // log the event 
        event(new ProjectTimerLogEvent(
            $message ,
            $authId, 

        ));

         

        // send system notifications to users 
            // check  ActivityLogHelper::sendSystemNotificationEvent() function to understand how to user this.

             $users_roles_to_notify = [
                'admin',
                'global_admin',
                'reviewer',
                'user'
            ];  

            // set custom users that will not be notified 
            $excluded_users = []; 
            // $excluded_users[] = Auth::user()->id ?? null; // exclude the current user to the notified user list 
            // $excluded_users[] = 72; // for testing only
            // dd($excluded_users);
            
        // send system notifications to users  


        $project_timer = ProjectTimer::first();
        // dd($project_timer );
        
        // send email about project timer update
        ProjectTimerLogHelper::sendEmailUpdateEvent(
            $project_timer->id,
            $users_roles_to_notify,
            [],
            'info',
            [],
            $route,


        ); 

        return 'Project review request submitted fired!';

    }



   
    

    //====================//
    //  test the email previews
    //====================//
 
 

    public function email_preview_submitted(){
        /* Submission email /*
            /**
             * Test email submission on the listener
             * 
             *  event(new Submitted(217, 73, true, true));
             * 
             *  try {
                    Mail::to($user->email)->queue(
                        new SubmittedMail($project, $project_document)
                    );
                } catch (\Throwable $e) {
                    // Log the error without interrupting the flow
                    Log::error('Failed to dispatch SubmittedMail mail: ' . $e->getMessage(), [
                        'project_document_id' => $project_document->id,
                        'project_id' => $project->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

            */

            /*
            $project_document_id = 217;

            $project_document = ProjectDocument::find($project_document_id); 
            $project = Project::find($project_document->project_id);
            // $user = User::find($event->authId);

            return new SubmittedMail($project,  $project_document);
            */
        /* Submission email /*




        /* New user verification  request*/
            /*
            // 9
            // Arthur Global Administrator Cervania
            // arthurcervania13@gmail.com
            
            //         121
            // Anne Swanson
            // gntf.org@gmail.com

            $user = User::find(id: 121);
            $userToNotify = User::find(id: 9);
            $viewUrl = UserLogHelper::getRoute('new-user-verification-request',$user->id);
            try {
                Mail::to($user->email)->queue(
                    new NewUserVerificationRequestMail($user, $userToNotify, $viewUrl )
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch NewUserVerificationRequestMail mail: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'userToNotify' => $userToNotify->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }


            return new NewUserVerificationRequestMail($user, $userToNotify, $viewUrl );

        /* ./ New user verification  request*/






        
        /* New User verified  */
             
            // 9
            // Arthur Global Administrator Cervania
            // arthurcervania13@gmail.com
            
            //         123
            // Anne Swanson
            // gntf.org@gmail.com

            $user = User::find(id: 123);
            $userToNotify = User::find(id: 123);
            $viewUrl = UserLogHelper::getRoute('new-user-verification-request',$user->id);
            try {
                Mail::to($user->email)->queue(
                    new NewUserVerifiedMail($user, $userToNotify, $viewUrl )
                );
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch NewUserVerifiedMail mail: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'userToNotify' => $userToNotify->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }


            return new NewUserVerifiedMail($user, $userToNotify, $viewUrl );

        /* ./ New User verified  */










    }











 
}
