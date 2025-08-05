<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectController extends Controller
{


    // Note that the control of that data displayed is on the Class of the project.list 
    // You are just adding the route in order to not make any route mistakes


    // own 
        // index
        public function index(){

            $user = Auth::user();
            // Check if the user has the role "system access global admin" OR the permission "project list view"
            if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project list view'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->route('dashboard');
                
                
            }
    
            return view('admin.project.index',[
                'route' => 'project.index'
            ]);
        }

        // index_update_pending
        public function index_update_pending(){

            $user = Auth::user();
            // Check if the user has the role "system access global admin" OR the permission "project list view"
            if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project list view update pending'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->route('dashboard');
                
                
            }
    
            return view('admin.project.index',[
                'route' => 'project.index.update-pending'
            ]);
        }

        // index_review_pending
        public function index_review_pending(){

            $user = Auth::user();
            // Check if the user has the role "system access global admin" OR the permission "project list view"
            if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project list view review pending'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->route('dashboard');
                
                
            }
    
            return view('admin.project.index',[
                'route' => 'project.index.review-pending'
            ]);
        }

 




    // ./ own


    // all
        // index_all
        public function index_all(){

            $user = Auth::user();
            // Check if the user has the role "system access global admin" OR the permission "project list view"
            if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project list view'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->route('dashboard');
                
                
            }
    
            return view('admin.project.index',[
                'route' => 'project.index.all'
            ]);
        }


        // index_all_no_drafts
        public function index_all_no_drafts(){

            $user = Auth::user();
            // Check if the user has the role "system access global admin" OR the permission "project list view"
            if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project list view all no drafts'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->route('dashboard');
                
                
            }
    
            return view('admin.project.index',[
                'route' => 'project.index.all.no-drafts'
            ]);
        }



        // index_update_pending_all_linked
        public function index_update_pending_all_linked(){

            $user = Auth::user();
            // Check if the user has the role "system access global admin" OR the permission "project list view"
            if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project list view update pending all linked'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->route('dashboard');
                
                
            }
    
            return view('admin.project.index',[
                'route' => 'project.index.update-pending.all-linked'
            ]);
        }

        // index_review_pending_all_linked
        public function index_review_pending_all_linked(){

            $user = Auth::user();
            // Check if the user has the role "system access global admin" OR the permission "project list view"
            if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project list view review pending all linked'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->route('dashboard');
                
                
            }
    
            return view('admin.project.index',[
                'route' => 'project.index.review-pending.all-linked'
            ]);
        }


        // index_update_pending_all
        public function index_update_pending_all(){

            $user = Auth::user();
            // Check if the user has the role "system access global admin" OR the permission "project list view"
            if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project list view update pending all'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->route('dashboard');
                
                
            }
    
            return view('admin.project.index',[
                'route' => 'project.index.update-pending.all'
            ]);
        }

        // index_review_pending_all
        public function index_review_pending_all(){

            $user = Auth::user();
            // Check if the user has the role "system access global admin" OR the permission "project list view"
            if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project list view review pending all'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->route('dashboard');
                
                
            }
    
            return view('admin.project.index',[
                'route' => 'project.index.review-pending.all'
            ]);
        }


        // index_open_review
        public function index_open_review(){

            $user = Auth::user();
            // Check if the user has the role "system access global admin" OR the permission "project list view"
            if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project list view open review'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->route('dashboard');
                
                
            }
    
            return view('admin.project.index',[
                'route' => 'project.index.open-review'
            ]);
        }


    // ./all






    // my_projects_index
    // public function my_projects_index(){

    //     $user = Auth::user();
    //     // Check if the user has the role "system access global admin" OR the permission "project list view"
    //     if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project list view'))) {
    //         Alert::error('Error', 'You do not have permission to access this section.');

    //         // If there is no previous URL, redirect to the dashboard
    //         return redirect()->route('dashboard');
               
             
    //     }
 
    //     return view('admin.project.my-projects.index');
    // }

 

 

    public function in_review(){

        $user = Auth::user();
        // Check if the user has the role "system access global admin" OR the permission "project review list view"
        if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project review list view'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }


        return view('admin.project.in_review');

    }


    public function my_projects_in_review(){

        $user = Auth::user();
        // Check if the user has the role "system access global admin" OR the permission "project review list view"
        if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project review list view'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }


        return view('admin.project.my-projects.in_review');

    }



    public function review($id){
         
        $user = Auth::user();
        $project = Project::findOrFail($id);


        // check if the current project reviewer is an open review
        if(!empty($project->getCurrentReviewer()) && empty($project->getCurrentReviewer()->user_id) ){
            // Check if the user has the role "system access global admin" OR the permission "project review"
            if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project review create'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->to(url()->previous() ?? route('dashboard'));
                
                
            }


            //

            // dd("Open Review");

            $project_reviewer = $project->getCurrentReviewer();
            $project_reviewer->user_id = Auth::user()->id;
            $project_reviewer->updated_at = now();
            $project_reviewer->updated_by = Auth::user()->id;
            $project_reviewer->save();




        }



        // Check if the user has the role "system access global admin" OR the permission "project review"
        if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project review create'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->to(url()->previous() ?? route('dashboard'));
               
             
        }


 
       


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
        // Check if the user has the role "system access global admin" OR the permission "project create"
        if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project create'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }

        return view('admin.project.create');
    }

    // edit
    public function edit($id){

        $user = Auth::user();
        // Check if the user has the role "system access global admin" OR the permission "project edit"
        if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project edit'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }

        $project = Project::findOrFail($id);

        // cehck if the user is the project creator and check if the project is allowing project submissions 
        // if true, it means that the project can be edited 
        // if false, it means the project must first be reviewed before the project creator can edit the project
        if($project->allow_project_submission == false && $user->id == $project->created_by){
            Alert::error('Error', 'Your project is submitted and to be reviewed by the reviewer. Editing is prohibited. Please wait until the review is complete');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('project.index');
        }
        



        
        return view('admin.project.edit',compact('project'));
    }


    public function show($id){
 
        $user = Auth::user();
        // Check if the user has the role "system access global admin" OR the permission "project view"
        if (!$user ||
        
            (!$user->can('system access global admin') && !$user->hasPermissionTo('project view')) 
            
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
        // Check if the user has the role "system access global admin" OR the permission "project update list view"
        if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project update list view'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }


        return view('admin.project.pending_project_update');
    }

    // list of projects that are pending update by users
    public function my_projects_pending_project_update(){

        $user = Auth::user();
        // Check if the user has the role "system access global admin" OR the permission "project update list view"
        if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('project update list view'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->route('dashboard');
               
             
        }


        return view('admin.project.my-projects.pending_project_update');
    }

    

    // // add_permissions
    // public function add_permissions($id){
    //     $role = Role::findOrFail($id);
    //     return view('admin.role.add_permissions',compact('role'));

    // }



}
