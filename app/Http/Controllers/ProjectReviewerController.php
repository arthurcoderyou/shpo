<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\ProjectDocument;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectReviewerController extends Controller
{
    // index
    public function index($id){
        $project = Project::findOrFail($id);


        // Check if the user has the role "system access global admin" OR the permission "project edit"
        if ($project->status == "draft") {
            Alert::error('Error', 'The project is still in draft and havent been submitted yet.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->back();
               
             
        }


        // Check if the project does not have any project reviewers due to an update
        // if the project is on que, do not add it on the condition because during on que, there is really no project reviewers
        if ( $project->status == "on_que") {
            Alert::error('Error', 'The project reviewers are not set yet because project is still on que. Wait for it to be submitted automatically on working days and hours');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->back();
               
             
        }

        if(empty($project->project_reviewers->count())){
            Alert::error('Error', 'The project reviewers are missing for this project due to an update. Please wait for the administrator to fix this issue');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->back();
        }



        return view('admin.project_reviewer.index',['project' => $project]);

    }



    public function project_reviewer_index($project_document_id){

        $project_document = ProjectDocument::findOrFail($project_document_id);

        $project = Project::findOrFail( $project_document->project_id);


        // Check if the user has the role "system access global admin" OR the permission "project edit"
        if ($project_document->status == "draft") {
            Alert::error('Error', 'The project document is still in draft and havent been submitted yet.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->back();
               
             
        }


        // Check if the project does not have any project reviewers due to an update
        // if the project is on que, do not add it on the condition because during on que, there is really no project reviewers
        if ( $project_document->status == "on_que") {
            Alert::error('Error', 'The project document reviewers are not set yet because project document is still on queue. Wait for it to be submitted automatically on working days and hours');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->back();
               
             
        }

        if(empty($project_document->project_reviewers->count())){
            Alert::error('Error', 'The project document reviewers are missing for this project document due to an update. Please wait for the administrator to fix this issue');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->back();
        }



        return view('admin.project_document_reviewer.index',[
            'project' => $project,
            'project_document' => $project_document
        ]);

    }







    //create
    public function create(){

        return view('admin.project_reviewer.create');
    }

}
