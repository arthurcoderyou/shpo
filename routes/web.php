<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ArcGISController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\ReReviewController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProjectTimerController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectReviewerController;
use App\Http\Controllers\TwoFactorVerificationController;


Route::view('/', 'welcome')->name('welcome');


// Route::post('/user-activity', function (\Illuminate\Http\Request $request) {
//     // You can store or log the activity here
//     // Example: 
//     Cache::put("user-last-activity-{$request->userId}", now());
//     return response()->json(['status' => 'ok']);
// });

 
Route::middleware(['throttle:60,1','verified'])->group(function () {
    Route::middleware(['auth' ,'log_user_device'])->group(function () {

        # 2fA Verify
            // Route::get('/2fa/verify', [TwoFactorVerificationController::class, 'verify'])->name('2fa.verify');
        # ./ 2fA Verify

        // 2fa middleware 
        Route::middleware(['2fa'])->group(function () { 
            #   dashboard
            // Route::view('dashboard', 'dashboard')
            //     ->middleware(['auth'])
            //     ->name('dashboard');

            Route::get('dashboard',[DashboardController::class, 'dashboard'])->name('dashboard')->middleware(['auth']);

            #   ./ dashboard

            #   profile
                Route::view('profile', 'profile')
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:profile update information,permission:profile update password'])
                    ->name('profile');
            #   ./ profile

            #   activity_logs
                Route::get('activity_logs',[ActivityLogController::class, 'index'])
                    ->middleware('role.permission.alert:role:,permission:system access global admin,permission:activity log list view')
                    ->name('activity_logs.index');

            #   ./ activity_logs

            #   user
                Route::get('user',[UserController::class, 'index'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:user list view'])
                    ->name('user.index');
                Route::get('user/create',[UserController::class, 'create'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:user create'])
                    ->name('user.create');
                Route::get('user/{user}/edit',[UserController::class, 'edit'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:user edit'])
                    ->name('user.edit');
                Route::get('user/{user}/edit_role',[UserController::class, 'edit_role'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:user edit'])
                    ->name('user.edit_role');
            #   ./ user


            # role
                Route::get('role',[RoleController::class, 'index'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:role list view'])
                    ->name('role.index');
                Route::get('role/create',[RoleController::class, 'create'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:role create'])
                    ->name('role.create');
                Route::get('role/{role}/edit',[RoleController::class, 'edit'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:role edit'])
                    ->name('role.edit');
                Route::get('role/{role}/add_permissions',[RoleController::class, 'add_permissions'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:role view permission'])
                    ->name('role.add_permissions');
            # ./ role

            # permission
                Route::get('permission',[PermissionController::class, 'index'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:permission list view'])
                    ->name('permission.index');
                Route::get('permission/create',[PermissionController::class, 'create'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:permission create'])    
                    ->name('permission.create');
                Route::get('permission/{permission}/edit',[PermissionController::class, 'edit'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:permission edit'])    
                    ->name('permission.edit');
            # ./ permission



            # project


                # project own
                    Route::get('project',[ProjectController::class, 'index'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project list view'])  
                        ->name('project.index');

                    Route::get('project/update_pending',[ProjectController::class, 'index_update_pending'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project list view update pending'])  
                        ->name('project.index.update-pending');

                     Route::get('project/review_pending',[ProjectController::class, 'index_review_pending'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project list view review pending'])  
                        ->name('project.index.review-pending');





                    Route::get('project/create',[ProjectController::class, 'create'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project create']) 
                        ->name('project.create');

                    Route::get('project/{project}/edit',[ProjectController::class, 'edit'])
                            ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project edit']) 
                            ->name('project.edit');
                            
                    Route::get('project/{project}/show',[ProjectController::class, 'show'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project view']) 
                        ->name('project.show');    
                    

                    # project - project document section (strictly connected to a project )
                        // show the project documents  
                        Route::get('project/{project}/show/project_documents', [ProjectController::class, 'project_documents'])
                            ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin,permission:project list view,permission:system access reviewer']) 
                            ->name('project.project-document.index');

                        

                        // // show the project document attachments 
                        // Route::get('project/{project}/project_document/{project_document}/show',[ProjectDocumentController::class, 'show'])
                        //     ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project document list view']) 
                        //     ->name('project.project-document.show'); // for users to see projects pending updates and resubmission




                    # ./ project - project document section (strictly connected to a project )

                # ./ own


                # all
                    Route::get('project/all',[ProjectController::class, 'index_all'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project list view all'])  
                        ->name('project.index.all');

                    Route::get('project/all/no_drafts',[ProjectController::class, 'index_all_no_drafts'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project list view all no drafts'])  
                        ->name('project.index.all.no-drafts');

                    Route::get('project/update_pending/all/linked',[ProjectController::class, 'index_update_pending_all_linked'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project list view update pending all linked'])  
                        ->name('project.index.update-pending.all-linked');

                    Route::get('project/review_pending/all/linked',[ProjectController::class, 'index_review_pending_all_linked'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project list view review pending all linked'])  
                        ->name('project.index.review-pending.all-linked');


                    Route::get('project/update_pending/all',[ProjectController::class, 'index_update_pending_all'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project list view update pending all'])  
                        ->name('project.index.update-pending.all');

                    Route::get('project/review_pending/all',[ProjectController::class, 'index_review_pending_all'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project list view review pending all'])  
                        ->name('project.index.review-pending.all');

                    Route::get('project/open_review',[ProjectController::class, 'index_open_review'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project list view open review'])  
                        ->name('project.index.open-review');

                # ./ all 


                // General Project routes (accessible by anyone )
                // for reviewers, admin and dsi god admin
                // Route::get('project',[ProjectController::class, 'index'])
                //     ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project all list view'])  
                //     ->name('project.index'); 

                // Route::get('project/in_review',[ProjectController::class, 'in_review'])
                //     ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project review all list view']) 
                //     ->name('project.in_review'); // for reviewers to see projects pending reviews 


                // Route::get('project/pending_project_update',[ProjectController::class, 'pending_project_update'])
                //     ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project update all list view']) 
                //     ->name('project.pending_project_update'); // for users to see projects pending updates and resubmission


                
                

                // WILL BE NOT USED 
                Route::get('project/{project}/review',[ProjectController::class, 'review'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project review create']) 
                    ->name('project.review');

                




                // show the project documents - with and without a project 
                Route::get('project_documents/{project?}', [ProjectController::class, 'project_documents'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin,permission:project list view,permission:system access reviewer']) 
                    ->name('project.project_documents');


                // show the project documents - that are on open review
                Route::get('project_documents_open/{project?}', [ProjectController::class, 'project_documents_open'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin,permission:project list view,permission:system access reviewer']) 
                    ->name('project.project_documents.open');

                // review project document
                Route::get('project_document/{project_document}/review', [ProjectController::class, 'project_document_review'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin,permission:project review create']) 
                    ->name('project.project_document_review');
                


            # ./ project

            # project document

                // list of project documents with or without project
                Route::get('project_document/{project?}/{project_document?}',[ProjectDocumentController::class, 'index'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin,permission:system access reviewer,permission:project document create']) 
                    ->name('project-document.index'); // for users to see projects pending updates and resubmission

                // list of open project documents with or without project
                Route::get('project_document_open_review/{project?}/{project_document?}',[ProjectDocumentController::class, 'index_open_review'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin,permission:project document create']) 
                    ->name('project-document.index.open-review'); // for users to see projects pending updates and resubmission


                // list of review pending project documents with or without project
                Route::get('project_document_review_pending/{project?}/{project_document?}',[ProjectDocumentController::class, 'index_review_pending'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin,permission:system access reviewer,permission:project document create']) 
                    ->name('project-document.index.review-pending'); // for users to see projects pending updates and resubmission

                // submitter
                // list of  project documents with changes requested with or without project
                Route::get('project_document_changes_requested/{project?}/{project_document?}',[ProjectDocumentController::class, 'index_changes_requested'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin,permission:system access reviewer,permission:project document create']) 
                    ->name('project-document.index.changes-requested'); // for users to see projects pending updates and resubmission

                // submitter
                // list of  project documents with changes requested with or without project
                Route::get('project_document_pending_review/{project?}/{project_document?}',[ProjectDocumentController::class, 'index_pending_review'])
                    // ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin,permission:system access reviewer,permission:project document create']) 
                    ->middleware(['role.permission.alert:role:,permission:system access user']) 
                    ->name('project-document.index.pending-review'); // for users to see projects pending updates and resubmission

                    


                // review project document
                Route::get('project_document_review/{project?}/{project_document?}',[ProjectDocumentController::class, 'review'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin,permission:system access reviewer,permission:project document create']) 
                    ->name('project-document.review'); // for users to see projects pending updates and resubmission


                // create new project document and attachments 
                Route::get('project/{project}/project_document_create',[ProjectDocumentController::class, 'create'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project document create']) 
                    ->name('project.project-document.create'); // for users to see projects pending updates and resubmission

                

                // show the project - project document attachments 
                Route::get('project/{project}/project_document/{project_document}/show',[ProjectDocumentController::class, 'show'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project document list view']) 
                    ->name('project.project-document.show'); // for users to see projects pending updates and resubmission

                

                // update new project - project document and add new attachments 
                Route::get('project/{project}/project_document_edit_attachments/{project_document}/',[ProjectDocumentController::class, 'edit'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project document edit']) 
                    ->name('project.project_document.edit_attachments'); // for users to see projects pending updates and resubmission

            # ./ project document 
 
            # reviewer
                Route::get('project/{project}/project_reviewer/',[ProjectReviewerController::class,'index'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project reviewer list view']) 
                    ->name('project.reviewer.index'); 


                // project document reviewer
                Route::get('project_document_reviewer/{project_document}',[ProjectReviewerController::class,'project_reviewer_index'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin,permission:project reviewer list view']) 
                    ->name('project.document.reviewer.index'); 


            # ./ reviewer

            # project_timer
                Route::get('project_timer',[ProjectTimerController::class,'index'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:timer list view']) 
                    ->name('project_timer.index'); 
            # ./ project_timer
            
            # reviewer
                Route::get('reviewer',[ReviewerController::class,'index'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:reviewer list view']) 
                    ->name('reviewer.index');
                // Route::get('reviewer/create',[ReviewerController::class, 'create'])->name('reviewer.create');
                // Route::get('reviewer/{reviewer}/edit',[ReviewerController::class, 'edit'])->name('reviewer.edit');
            # ./ reviewer


            # reviews 
                Route::get('review',[ReviewController::class,'index'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:review list view']) 
                    ->name('review.index');

                Route::get('project_document_review_flow/{project_document}',[ReviewController::class,'review_flow'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:review list view']) 
                    ->name('project_document.review.flow');


            # ./ reviews

            # re-reviews 
                Route::get('re-review',[ReReviewController::class,'index'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin']) 
                    ->name('re-review.index');
                Route::get('re-review/{rereview}/show',[ReReviewController::class,'show'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:system access admin']) 
                    ->name('re-review.show');
            # ./ re-reviews 


            # document_types 
                Route::get('document_type',[DocumentTypeController::class,'index'])
                ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:document type list view']) 
                ->name('document_type.index');
                Route::get('document_type/{document_type}',[DocumentTypeController::class,'edit'])
                ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:document type edit']) 
                ->name('document_type.edit');
            # ./ document_types

            # setting 
                Route::get('setting',[SettingController::class,'index'])
                ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                ->name('setting.index'); 
                Route::get('setting/{setting}',[SettingController::class,'edit'])
                ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                ->name('setting.edit');

                Route::get('setting_manager',[SettingController::class,'manager'])
                ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                ->name('setting.manager');
                Route::get('setting_manager/{setting}/edit',[SettingController::class,'manager_edit'])
                ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                ->name('setting.manager.edit');

            # ./ setting



            # notifications
                Route::get('/notifications/read-and-open/{id}', [NotificationController::class, 'markAsReadandOpen'])
                    ->name('notifications.read');
            # ./ notifications


            # file manager 
                Route::get('/file_manager/attachment',[AttachmentController::class,'index'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                    ->name('file_manager.attachment.index');
                Route::get('/file_manager/attachment/create',[AttachmentController::class,'create'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                    ->name('file_manager.attachment.create');
                Route::get('/file_manager/attachment/{attachment}',[AttachmentController::class,'edit'])
                    ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                    ->name('file_manager.attachment.edit');
            # ./ file manager




            // # forum 

            //     Route::get('forum',[ForumController::class,'index'])->name('forum.index');
            //     // Route::get('forum/create',[ForumController::class,'create'])->name('forum.create');
            //     Route::get('forum/{forum}/edit',[ForumController::class, 'edit'])
                    // ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project edit']) 
            //         ->name('forum.edit');
                
            // # ./ forum


            // # discussion 

            //     Route::get('discussion',[DiscussionController::class,'index'])->name('discussion.index');
            //     // Route::get('discussion/create',[DiscussionController::class,'create'])->name('discussion.create');
            //     Route::get('discussion/{discussion}/edit',[DiscussionController::class, 'edit'])
                    // ->middleware(['role.permission.alert:role:,permission:system access global admin,permission:project edit']) 
            //         ->name('discussion.edit');
                
            // # ./ discussion


            




            # map
                // Route::get('map',[MapController::class,'index'])->name('map.index');

                Route::get('map/openlayer',[MapController::class,'open_layer_list'])->name('map.open_layer_list');
                
                // Route::get('map/create',[MapController::class,'create'])->name('map.create');
            # ./ map

            # arcgis 

                Route::get('map/arcgis',[MapController::class,'index_arcgis'])->name('map.arc_gis'); 


                Route::get('/map', [ArcGISController::class, 'showMap'])->name('arcgis.map');
                
            # ./ arcgis



            # ftp

                Route::get('/ftp-download/{id}', [AttachmentController::class, 'ftpDownload'])->name('ftp.download');
            # ./ ftp


            # testing links     
                // layout test routes
                    // test: projects list interface
                    Route::get('test/project',[TestingController::class, 'test_project'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.project.index'); // for users to see projects pending updates and resubmission

                    // test: project documents interface
                    Route::get('test/project_document',[TestingController::class, 'test_project_document'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.project_document'); // for users to see projects pending updates and resubmission

                    // test: project document review interface
                    Route::get('test/project_document_review',[TestingController::class, 'test_project_document_review'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.project_document.review'); // for users to see projects pending updates and resubmission

                    // test: projecttable
                    Route::get('test/project/table',[TestingController::class, 'test_project_table'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.project.table'); // for users to see projects pending updates and resubmission

                    // test: project show 
                    Route::get('test/project/show',[TestingController::class, 'test_project_show'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.project.show'); // for users to see projects pending updates and resubmission
                    // test: project show 

                    Route::get('test/project/show_2',[TestingController::class, 'test_project_show_2'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.project.show.2'); // for users to see projects pending updates and resubmission

                    Route::get('test/review/list',[TestingController::class, 'test_review_list'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.review.list'); // for users to see review list 

                    Route::get('test/review/re_review',[TestingController::class, 're_review'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.review.re_review'); // for users to see review list 


                    Route::get('test/review/digital_signature',[TestingController::class, 'digital_signature'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.review.digital_signature'); // for users to see review list 


                    Route::get('test/review/review_list_layout',[TestingController::class, 'review_list_layout'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.review.review_list_layout'); // for users to see review list 
                // ./ layout test routes
                    

                    
            


                // event temporary routes  

                    Route::get('test/event/project-document-submitted',[TestingController::class, 'testEventProjectDocumentSubmitted'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.testEventProjectDocumentSubmitted'); // for users to see projects pending updates and resubmission

                    Route::get('test/event/submitted',[TestingController::class, 'testEventSubmitted'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.testEventSubmitted'); // for users to see projects pending updates and resubmission


                    Route::get('test/event/review-request',[TestingController::class, 'testReviewRequest'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.testReviewRequest'); // for users to see projects pending updates and resubmission

                    Route::get('test/event/open-review-request',[TestingController::class, 'testOpenReviewRequest'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.testOpenReviewRequest'); // for users to see projects pending updates and resubmission

                    Route::get('test/event/followup-review-request',[TestingController::class, 'testFollowupReviewRequest'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.testFollowupReviewRequest'); // for users to see projects pending updates and resubmission
                    
                    Route::get('test/event/reviewer-list-updated',[TestingController::class, 'testReviewerListUpdated'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.testReviewerListUpdated'); // for users to see projects pending updates and resubmission

                    Route::get('test/event/reviewed-notification',[TestingController::class, 'testReviewedNotification'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.testReviewedNotification'); // for users to see projects pending updates and resubmission

                    Route::get('test/event/review-submitted-notification',[TestingController::class, 'testReviewSubmittedNotification'])
                        ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                        ->name('test.testReviewSubmittedNotification'); // for users to see projects pending updates and resubmission

                            

                // ./ event temporary routes

                    Route::get('test/attachment/create',[AttachmentController::class, 'create'])
                            ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                            ->name('test.attachment.create');  



                // 


                // email testing routes
                    Route::get('test/email_preview/submitted',[TestingController::class, 'email_preview_submitted'])
                            ->middleware(['role.permission.alert:role:,permission:system access global admin']) 
                            ->name('test.email_preview.submitted');  
                // ./ email testing routes


            #./ testing links 


        
        }); //2fa middleware 

    });

});

require __DIR__.'/auth.php';
