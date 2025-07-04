<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\ProjectTimerController;
use App\Http\Controllers\ProjectDocumentController;
use App\Http\Controllers\ProjectReviewerController;
use App\Http\Controllers\TwoFactorVerificationController;


Route::view('/', 'welcome');


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
                    ->middleware(['role.permission:role:DSI God Admin,permission:profile update information,permission:profile update password'])
                    ->name('profile');
            #   ./ profile

            #   activity_logs
                Route::get('activity_logs',[ActivityLogController::class, 'index'])
                    ->middleware('role.permission:role:DSI God Admin,permission:activity log list view')
                    ->name('activity_logs.index');

            #   ./ activity_logs

            #   user
                Route::get('user',[UserController::class, 'index'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:user list view'])
                    ->name('user.index');
                Route::get('user/create',[UserController::class, 'create'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:user create'])
                    ->name('user.create');
                Route::get('user/{user}/edit',[UserController::class, 'edit'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:user edit'])
                    ->name('user.edit');
            #   ./ user


            # role
                Route::get('role',[RoleController::class, 'index'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:role list view'])
                    ->name('role.index');
                Route::get('role/create',[RoleController::class, 'create'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:role create'])
                    ->name('role.create');
                Route::get('role/{role}/edit',[RoleController::class, 'edit'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:role edit'])
                    ->name('role.edit');
                Route::get('role/{role}/add_permissions',[RoleController::class, 'add_permissions'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:role view permission'])
                    ->name('role.add_permissions');
            # ./ role

            # permission
                Route::get('permission',[PermissionController::class, 'index'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:permission list view'])
                    ->name('permission.index');
                Route::get('permission/create',[PermissionController::class, 'create'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:permission create'])    
                    ->name('permission.create');
                Route::get('permission/{permission}/edit',[PermissionController::class, 'edit'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:permission edit'])    
                    ->name('permission.edit');
            # ./ permission



            # project
                // General Project routes (accessible by anyone )
                    // for reviewers, admin and dsi god admin
                    Route::get('project',[ProjectController::class, 'index'])
                        ->middleware(['role.permission:role:DSI God Admin,permission:project all list view'])  
                        ->name('project.index'); 

                    Route::get('project/in_review',[ProjectController::class, 'in_review'])
                        ->middleware(['role.permission:role:DSI God Admin,permission:project review all list view']) 
                        ->name('project.in_review'); // for reviewers to see projects pending reviews 


                    Route::get('project/pending_project_update',[ProjectController::class, 'pending_project_update'])
                        ->middleware(['role.permission:role:DSI God Admin,permission:project update all list view']) 
                        ->name('project.pending_project_update'); // for users to see projects pending updates and resubmission


                    Route::get('project/create',[ProjectController::class, 'create'])
                        ->middleware(['role.permission:role:DSI God Admin,permission:project create']) 
                        ->name('project.create');
                    Route::get('project/{project}/edit',[ProjectController::class, 'edit'])
                        ->middleware(['role.permission:role:DSI God Admin,permission:project edit']) 
                        ->name('project.edit');
                    Route::get('project/{project}/show',[ProjectController::class, 'show'])
                        ->middleware(['role.permission:role:DSI God Admin,permission:project view']) 
                        ->name('project.show');


                    Route::get('project/{project}/review',[ProjectController::class, 'review'])
                        ->middleware(['role.permission:role:DSI God Admin,permission:project review']) 
                        ->name('project.review');


                    



                // Owner Project Routes (Specifically for Project Owners)
                    // for project owners (user, reviewer)
                    Route::get('project/my_projects',[ProjectController::class, 'my_projects_index'])
                        ->middleware(['role.permission:role:DSI God Admin,permission:project list view'])  
                        ->name('project.index.my-projects');

                    Route::get('project/in_review/my_projects',[ProjectController::class, 'my_projects_in_review'])
                        ->middleware(['role.permission:role:DSI God Admin,permission:project review list view']) 
                        ->name('project.in_review.my-projects'); // for reviewers to see projects pending reviews 

                    Route::get('project/pending_project_update/my_projects',[ProjectController::class, 'my_projects_pending_project_update'])
                        ->middleware(['role.permission:role:DSI God Admin,permission:project update list view']) 
                        ->name('project.pending_project_update.my-projects'); // for users to see projects pending updates and resubmission


                
            # ./ project

            # project document
                Route::get('project/{project}/project_document/{project_document}',[ProjectDocumentController::class, 'index'])
                    // ->middleware(['role.permission:role:DSI God Admin,permission:project update list view']) 
                    ->name('project.project_document'); // for users to see projects pending updates and resubmission
            # ./ project document 
 
            # reviewer
                Route::get('project/{project}/project_reviewer/',[ProjectReviewerController::class,'index'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:project reviewer list view']) 
                    ->name('project.reviewer.index'); 
            # ./ reviewer

            # project_timer
                Route::get('project_timer',[ProjectTimerController::class,'index'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:timer list view']) 
                    ->name('project_timer.index'); 
            # ./ project_timer
            
            # reviewer
                Route::get('reviewer',[ReviewerController::class,'index'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:reviewer list view']) 
                    ->name('reviewer.index');
                // Route::get('reviewer/create',[ReviewerController::class, 'create'])->name('reviewer.create');
                // Route::get('reviewer/{reviewer}/edit',[ReviewerController::class, 'edit'])->name('reviewer.edit');
            # ./ reviewer


            # reviews 
                Route::get('review',[ReviewController::class,'index'])
                    ->middleware(['role.permission:role:DSI God Admin,permission:review list view']) 
                    ->name('review.index');
            # ./ reviews


            # document_types 
                Route::get('document_type',[DocumentTypeController::class,'index'])
                ->middleware(['role.permission:role:DSI God Admin,permission:document type list view']) 
                ->name('document_type.index');
                Route::get('document_type/{document_type}',[DocumentTypeController::class,'edit'])
                ->middleware(['role.permission:role:DSI God Admin,permission:document type edit']) 
                ->name('document_type.edit');
            # ./ document_types



            // # forum 

            //     Route::get('forum',[ForumController::class,'index'])->name('forum.index');
            //     // Route::get('forum/create',[ForumController::class,'create'])->name('forum.create');
            //     Route::get('forum/{forum}/edit',[ForumController::class, 'edit'])
            //         // ->middleware(['role.permission:role:DSI God Admin,permission:project edit']) 
            //         ->name('forum.edit');
                
            // # ./ forum


            // # discussion 

            //     Route::get('discussion',[DiscussionController::class,'index'])->name('discussion.index');
            //     // Route::get('discussion/create',[DiscussionController::class,'create'])->name('discussion.create');
            //     Route::get('discussion/{discussion}/edit',[DiscussionController::class, 'edit'])
            //         // ->middleware(['role.permission:role:DSI God Admin,permission:project edit']) 
            //         ->name('discussion.edit');
                
            // # ./ discussion


            




            # map
                // Route::get('map',[MapController::class,'index'])->name('map.index');

                Route::get('map/openlayer',[MapController::class,'open_layer_list'])->name('map.open_layer_list');
                
                // Route::get('map/create',[MapController::class,'create'])->name('map.create');
            # ./ map


            # ftp

                Route::get('/ftp-download/{id}', [AttachmentController::class, 'ftpDownload'])->name('ftp.download');
            # ./ ftp
        
        }); //2fa middleware 

    });

});

require __DIR__.'/auth.php';
