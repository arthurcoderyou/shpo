<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectReviewerController extends Controller
{
    // index
    public function index($id){
        $project = Project::findOrFail($id);


        // Check if the user has the role "DSI God Admin" OR the permission "project edit"
        if ($project->status == "draft") {
            Alert::error('Error', 'The project is still in draft and havent been submitted yet.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->back();
               
             
        }


        return view('admin.project_reviewer.index',['project' => $project]);

    }

    //create
    public function create(){

        return view('admin.project_reviewer.create');
    }

}
