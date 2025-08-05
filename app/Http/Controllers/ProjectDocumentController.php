<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\ProjectDocument;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectDocumentController extends Controller
{
    public function index($project_id, $project_document_id){
        $project = Project::findOrFail($project_id);

        $user = Auth::user();
 


        $project_document = ProjectDocument::findOrFail($project_document_id);
        
        return view('admin.project_document.index',compact('project','project_document'));
        
    }

    public function create($project_id){
        $project = Project::findOrFail($project_id);

        $user = Auth::user();
 
        if($project->allow_project_submission == false && $user->id == $project->created_by){
            Alert::error('Error', 'Your project is submitted and to be reviewed by the reviewer. Editing is prohibited. Please wait until the review is complete');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('project.index');
       
        }

        
        return view('admin.project_document.create',compact('project'));
        
    }



    public function edit($project_id, $project_document_id){

        $project = Project::findOrFail($project_id);
        
        $user = Auth::user();

        
        if($project->allow_project_submission == false && $user->id == $project->created_by){
            Alert::error('Error', 'Your project is submitted and to be reviewed by the reviewer. Editing is prohibited. Please wait until the review is complete');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('project.index');
       
        }

        $project_document = ProjectDocument::findOrFail($project_document_id);
        
        return view('admin.project_document.edit',compact('project','project_document'));

    }
 


}
