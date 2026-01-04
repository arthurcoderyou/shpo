<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Arr; 
use App\Models\ProjectDocument;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ProjectDocumentHelpers;
use Illuminate\Support\Facades\Storage; 
use RealRashid\SweetAlert\Facades\Alert;


class ProjectDocumentController extends Controller
{
    public function index(){
        

        // return view('admin.project_document.index',compact('project','project_document'));

        $route = "project-document.index";

        return view('admin.project_document.index',compact('route'));
        
    }


    public function index_open_review(){
        

        // return view('admin.project_document.index',compact('project','project_document'));

        $route = "project-document.index.open-review";

        return view('admin.project_document.index',compact('route'));
        
    }

    public function index_pending_review(){
        

        // return view('admin.project_document.index',compact('project','project_document'));

        $route = "project-document.index.pending-review'";
        // dd("here");

        return view('admin.project_document.index',compact('route'));
        
    }


    public function index_changes_requested(){

        $route = "project-document.index.changes-requested";

        return view('admin.project_document.index',compact('route'));
    }


   



    public function create($project_id){
        $project = Project::findOrFail($project_id);


        $document_upload_location = Setting::getOrCreateWithDefaults('document_upload_location');
 





        $project = Project::findOrFail($project_id);
         
        $user = Auth::user();

        

        // // 1) Guard: setting must exist & have a value
        // $document_upload_location = Setting::getOrCreateWithDefaults('document_upload_location');
        // if (!$document_upload_location || blank($document_upload_location->value)) {
        //     Alert::error('Error', 'The Document Upload location has not been set by the administrator.');
        //     return redirect()->route('project.project-document.index', ['project' => $project->id]);
        // }

        // $disk = $document_upload_location->value;

        // // 2) Fast preflight check (no Flysystem call yet — avoids long hangs)
        // $diskCfg = config("filesystems.disks.$disk", []);
        // $driver  = Arr::get($diskCfg, 'driver');

        // // Default ports
        // $host = Arr::get($diskCfg, 'host');
        // $port = (int) Arr::get($diskCfg, 'port', $driver === 'sftp' ? 22 : 21);

        // // Only ping when we actually have a remote disk config
        // if (in_array($driver, ['ftp', 'sftp'], true) && $host) {
        //     $errno = 0; $errstr = '';
        //     // 3-second connect timeout — fail fast
        //     $conn = @fsockopen($host, $port, $errno, $errstr, 3);
        //     if (!$conn) {
        //         Alert::error('Error', "Connection can’t be established with the {$disk} server ($host:$port).");
        //         return redirect()->route('project.project-document.index', ['project' => $project->id]);
        //     }
        //     fclose($conn);
        // }

        // // 4) OPTIONAL: very light Flysystem probe that won’t hit a file
        // // Prefer directoryExists on an empty path (root). Still wrap in try/catch.
        // try {
        //     // NOTE: For Flysystem v3, directoryExists('') is valid and cheap.
        //     if (method_exists(Storage::disk($disk), 'directoryExists')) {
        //         Storage::disk($disk)->directoryExists('');
        //     } else {
        //         // As a fallback, listContents with a shallow depth
        //         Storage::disk($disk)->files('');
        //     }
        // } catch (\Throwable $e) {
        //     Alert::error('Error', "Storage check failed for {$disk}: " . $e->getMessage());
        //     return redirect()->route('project.project-document.index', ['project' => $project->id]);
        // }

 
 
        // 
        // if($project->allow_project_submission == false && $user->id == $project->created_by){
        //     Alert::error('Error', 'Your project is submitted and to be reviewed by the reviewer. Editing is prohibited. Please wait until the review is complete');

        //     // If there is no previous URL, redirect to the dashboard
        //     return redirect()->route('project.index');
       
        // }
        
        return view('admin.project_document.create',compact('project'));
        
    }


    /*
    public function edit($project_id, $project_document_id){

        $project = Project::findOrFail($project_id);


        $document_upload_location = Setting::getOrCreateWithDefaults('document_upload_location');

        if(empty($document_upload_location) && empty($document_upload_location->value) ){

            Alert::error('Error','The Document Upload location had not been set by the administrator');
            return redirect()->route('project.project-document.index',['project' => $project->id] );

        } 

         // Check FTP connection before processing
        try {
            Storage::disk($document_upload_location->value)->exists('/'); // Basic check
            // dd($document_upload_location->value." works");

        } catch (\Exception $e) {
            // Handle failed connection
            // logger()->error("FTP connection failed: " . $e->getMessage());
            // return; // Exit or show error as needed

            Alert::error('Error','Connection cannot be stablished with the '.$document_upload_location->value.' server');
            return redirect()->route('project.project-document.index',['project' => $project->id] );

        }




        $project_document = ProjectDocument::findOrFail($project_document_id);
        
        $user = Auth::user();

        
        if($project_document->allow_project_submission == false && $user->id == $project_document->created_by){
            Alert::error('Error', 'Your project document is submitted and to be reviewed by the reviewer. Editing is prohibited. Please wait until the review is complete');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('project-document.index');
       
        }

        $project = Project::findOrFail($project_id);
        
        return view('admin.project_document.edit',compact('project','project_document'));

    }
    */

    

    public function edit($project_id, $project_document_id)
    {
        $project = Project::findOrFail($project_id);
        $project_document = ProjectDocument::findOrFail($project_document_id);
        $user = Auth::user();

        // 1) Guard: disallow edit when submitted
        if ($project_document->allow_project_submission === false && $user->id === $project_document->created_by) {
            Alert::error('Error', 'Your project document is submitted and being reviewed. Editing is prohibited until the review is complete.');
            return redirect()->route('project-document.index');
        }

        // 2) Guard: setting must exist & have a value
        $document_upload_location = Setting::getOrCreateWithDefaults('document_upload_location');
        if (!$document_upload_location || blank($document_upload_location->value)) {
            Alert::error('Error', 'The Document Upload location has not been set by the administrator.');
            return redirect()->route('project.project-document.index', ['project' => $project->id]);
        }

        $disk = $document_upload_location->value;

        // 3) Fast preflight check (no Flysystem call yet — avoids long hangs)
        $diskCfg = config("filesystems.disks.$disk", []);
        $driver  = Arr::get($diskCfg, 'driver');

        // Default ports
        $host = Arr::get($diskCfg, 'host');
        $port = (int) Arr::get($diskCfg, 'port', $driver === 'sftp' ? 22 : 21);

        // Only ping when we actually have a remote disk config
        if (in_array($driver, ['ftp', 'sftp'], true) && $host) {
            $errno = 0; $errstr = '';
            // 3-second connect timeout — fail fast
            $conn = @fsockopen($host, $port, $errno, $errstr, 3);
            if (!$conn) {
                Alert::error('Error', "Connection can’t be established with the {$disk} server ($host:$port).");
                return redirect()->route('project.project-document.index', ['project' => $project->id]);
            }
            fclose($conn);
        }

        // 4) OPTIONAL: very light Flysystem probe that won’t hit a file
        // Prefer directoryExists on an empty path (root). Still wrap in try/catch.
        try {
            // NOTE: For Flysystem v3, directoryExists('') is valid and cheap.
            if (method_exists(Storage::disk($disk), 'directoryExists')) {
                Storage::disk($disk)->directoryExists('');
            } else {
                // As a fallback, listContents with a shallow depth
                Storage::disk($disk)->files('');
            }
        } catch (\Throwable $e) {
            Alert::error('Error', "Storage check failed for {$disk}: " . $e->getMessage());
            return redirect()->route('project.project-document.index', ['project' => $project->id]);
        }

        return view('admin.project_document.edit', compact('project', 'project_document'));
    }



    

    public function show($project_id, $project_document_id){
        $project = Project::findOrFail($project_id);

        $user = Auth::user();
  
        $project_document = ProjectDocument::findOrFail($project_document_id);
        
        return view('admin.project_document.show',compact('project','project_document'));
        
    }

    public function review($project_id, $project_document_id){
        $project = Project::findOrFail($project_id);
        

        // dd($project);


        $user = Auth::user();
  
        
        $project_document = ProjectDocument::findOrFail($project_document_id); 
        
        $review_accepted = false;
        $review_accepted = ProjectDocumentHelpers::verifyAndClearReviewSession($project_document->id, $user->id);



        $current_reviewer = $project_document->getCurrentReviewerByProjectDocument();


        if(!empty($current_reviewer)){
            // dd($current_reviewer);
            if( $current_reviewer->slot_type == "open" ){
            
 


                // restriction to avoid the error for admin that has opened the review and still on the review page without actually clicking and saving himself as the current reviewer


 

                // check if the current project reviewer is an open review
                if(!empty($current_reviewer) && empty($current_reviewer->user_id) ){
                    // Check if the user has the role "system access global admin" OR the permission "project review"
                    if (!$user || (!$user->can('system access global admin') && (!$user->can('system access admin')) && !$user->hasPermissionTo('project review create'))) {
                        Alert::error('Error', 'You do not have permission to access this section of open review.');

                        // If there is no previous URL, redirect to the dashboard
                        return redirect()->to(url()->previous() ?? route('project-document.index.open-review',[
                            'project' => $project->id,
                            'project_document' => $project_document->id,
                        ])); 
                    } 
                    // dd("Open Review");

                    if($review_accepted){

                        // dd("accepted");
                         $current_reviewer->user_id = Auth::user()->id;
                        $current_reviewer->updated_at = now();
                        $current_reviewer->updated_by = Auth::user()->id;
                        $current_reviewer->save();

                    }else{
                        //  dd("not accepted");
                    }
                   

        
                }  
            }
        }elseif($project_document->status !== "approved"){
            Alert::error('Error', 'There is something wrong in the reviewers. There is no current reviewer while the review is still ongoing');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->to(url()->previous() ?? route('project-document.index.open-review',[
                'project' => $project->id,
                'project_document' => $project_document->id,
            ])); 

        }
        



        // Check if the user has the role "system access global admin" OR the permission "project review"
        if (
                !$user || ( 
                    !$user->hasPermissionTo('system access global admin') && 
                    !$user->hasPermissionTo('project review create') && 
                    !$user->hasPermissionTo('system access admin') && 
                    !$user->hasPermissionTo('system access reviewer') 
                )
            ) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard 
            return redirect()->to(url()->previous() ?? route('project-document.index.open-review',[
                'project' => $project->id,
                'project_document' => $project_document->id,
            ]));  
             
        }

 
        // update the status of the project if it is submitted and when the reviewer enter this view, the new status should be in_review
        if($project_document->status == "submitted"){
            $project_document->status = "in_review";
            $project_document->save();
 
            
        }


 
        
        return view('admin.project_document.review',compact('project','project_document'));
        
    }

}
