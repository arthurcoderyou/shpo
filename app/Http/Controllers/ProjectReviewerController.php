<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectReviewerController extends Controller
{
    // index
    public function index($id){
        $project = Project::findOrFail($id);

        return view('admin.project_reviewer.index',['project' => $project]);

    }

    //create
    public function create(){

        return view('admin.project_reviewer.create');
    }

}
