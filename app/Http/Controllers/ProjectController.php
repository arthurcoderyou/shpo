<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectController extends Controller
{
    // index
    public function index(){

        $user = Auth::user();
        // Check if the user has the role "DSI God Admin" OR the permission "project list view"
        if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('project list view'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }

        return view('admin.project.index');
    }

    public function in_review(){

        $user = Auth::user();
        // Check if the user has the role "DSI God Admin" OR the permission "project review list view"
        if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('project review list view'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }


        return view('admin.project.in_review');

    }

    public function review($id){
        
        $user = Auth::user();
        // Check if the user has the role "DSI God Admin" OR the permission "project review"
        if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('project review'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }


 
        $project = Project::findOrFail($id);


        // update the status of the project if it is submitted and when the reviewer enter this view, the new status should be in_review
        if($project->status == "submitted"){
            $project->status = "in_review";
            $project->save();
        }




        // check if the user is a reviewer 
        $isReviewer = $project->project_reviewers()->where('user_id', auth()->id())->exists();

        if(!$isReviewer){
            Alert::error('Error', 'You are not a reviewer for this project');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('project.in_review');
        }



        return view('admin.project.review',compact('project'));

    }


    // create
    public function create(){
        $user = Auth::user();
        // Check if the user has the role "DSI God Admin" OR the permission "project create"
        if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('project create'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }

        return view('admin.project.create');
    }

    // edit
    public function edit($id){

        $user = Auth::user();
        // Check if the user has the role "DSI God Admin" OR the permission "project edit"
        if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('project edit'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }

        $project = Project::findOrFail($id);
        return view('admin.project.edit',compact('project'));
    }


    public function show($id){
 
        $user = Auth::user();
        // Check if the user has the role "DSI God Admin" OR the permission "project view"
        if (!$user ||
        
            (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('project view')) 
            
            ) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }


        $project = Project::findOrFail($id);

        


        return view('admin.project.show',compact('project'));

    }


    // list of projects that are pending update by users
    public function pending_project_update(){

        $user = Auth::user();
        // Check if the user has the role "DSI God Admin" OR the permission "project update list view"
        if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('project update list view'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }


        return view('admin.project.pending_project_update');
    }
    

    // // add_permissions
    // public function add_permissions($id){
    //     $role = Role::findOrFail($id);
    //     return view('admin.role.add_permissions',compact('role'));

    // }



}
