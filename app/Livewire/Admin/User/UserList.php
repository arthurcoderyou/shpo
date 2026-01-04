<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Services\CacheService;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use App\Events\User\UserLogEvent;
use App\Models\ProjectAttachments;
use App\Models\ProjectSubscriber; 
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use RealRashid\SweetAlert\Facades\Alert;
use App\Livewire\Admin\Project\ProjectReview;
use App\Helpers\ActivityLogHelpers\UserLogHelper;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Helpers\SystemNotificationHelpers\UserNotificationHelper;

class UserList extends Component
{


    use WithFileUploads;
    use WithPagination;

    protected $listeners =[

        'userEvent' => '$refresh',

        // 'userCreated' => '$refresh',
        // 'userUpdated' => '$refresh',
        // 'userDeleted' => '$refresh',

        // 'systemEvent' => '$refresh',
    ];

    public $search = '';
    public $sort_by = '';
    public $record_count = 10;

    public $selected_records = [];
    public $selectAll = false;

    public $count = 0;

    public $file;

    public $selected_role;
    public $roles;

    public $role_request;

    public $user_count;

    public $user_status_filter = 'active'; // options: active, deactivated, all

    public array $role_options = [];

    public array $role_request_options = [
        "" => "Sort Request",
        "user" => "User Request",
        "reviewer" => "Reviewer Request" 
    ];

    public array $user_status_filter_options = [
        "all" => "All Users",
        "active" => "Active Users",
        "deactivated" => "Deactivated Users"   
    ];


    public array $sorting_options = [
        "" => "Sort by",
        "Name A - Z" => "Name A - Z",
        "Name Z - A" => "Name Z - A",
        "Email A - Z" => "Email A - Z",
        "Email Z - A" => "Email Z - A",
        "Latest Added" => "Latest Added",
        "Oldest Added" => "Oldest Added",
        "Latest Updated" => "Latest Updated",
        "Oldest Updated" => "Oldest Updated", 
    ];


    public function mount(){
        $this->selected_role = request()->query('selected_role', ''); // Default to empty string if not set
        $this->role_request = request()->query('role_request', ''); // Default to empty string if not set

        
        $roles = Role::query();
        
       if (!Auth::user()->can('system access global admin')) {
            // Show roles that DO NOT HAVE the 'system access global admin' permission
            $roles = $roles->whereDoesntHave('permissions', function ($query) {
                $query->where('name', 'system access global admin');
            });
        }

        $this->roles = $roles->get();


        foreach($this->roles as $role){

            $this->role_options[$role->id] =  $role->name;

        }

        // dd($this->role_options);

        // update the cache
        CacheService::updateUserStats();

    }



    public function deleteSelected()
    {
        // Fetch all projects created by the selected users
        $projects = Project::whereIn('created_by', $this->selected_records)->get();
    
        foreach ($projects as $project) {
            // Delete related project details
            $project->project_subscribers()->delete();
            $project->project_documents()->delete();
            $project->attachments()->delete();
            $project->project_reviewers()->delete();
            $project->project_reviews()->delete();
    
            // Finally, delete the project itself
            $project->delete();
        }
    
        // Delete the selected users
        User::whereIn('id', $this->selected_records)->delete();
    
        // Clear selected records
        $this->selected_records = [];
    
        // Show success message
        Alert::success('Success', 'Selected users and their projects deleted successfully');
        return redirect()->route('user.index');
    }

    // This method is called automatically when selected_records is updated
    public function updateSelectedCount()
    {
        // Update the count when checkboxes are checked or unchecked
        $this->count = count($this->selected_records);
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selected_records = User::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }

    public function delete($id){
        $user = User::find($id);




        if(!Auth::user()->can('system access global admin')){ // the God Admin can override this 

            if ($this->hasConnectedRecords($user)) { 

                // Alert::error('Error', 'User cannot be deleted because they are connected to existing records.');
                return redirect()->route('user.index')
                    ->with('alert.error','User cannot be deleted because they are connected to existing records.');
            }

        }
 

        // dd("All Goods");

        // Fetch all projects created by the selected users
        $projects = Project::where('created_by', $user->id)->get();
    
        foreach ($projects as $project) {
            // Delete related project details
            $project->project_subscribers()->delete();
            $project->project_documents()->delete();
            $project->attachments()->delete();
            $project->project_reviewers()->delete();
            $project->project_reviews()->delete();
            
    
            // Finally, delete the project itself
            $project->delete();
        }



        // for projects that the user is currently part with 
        $activity_logs = ActivityLog::where('created_by',$user->id)->get();
        foreach ($activity_logs as $activity_log) {
            
            $activity_log->delete();
        }


        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = UserLogHelper::getActivityMessage('deleted', $user->id, $authId);

            // get the route
            $route = UserLogHelper::getRoute('deleted', $user->id);

            // log the event 
            event(new UserLogEvent(
                $message ,
                $authId, 

            ));
    
            /** send system notifications to users */
                
                UserNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications


        $user->delete();    
        $user->notifications()->delete();

 

       






        // Alert::success('Success','User deleted successfully');
        return redirect()->route('user.index')
            ->with('alert.success',$message);
            ;

    }


    /** Actions with Password Confirmation panel */
        public $passwordConfirm = '';
        public $passwordError = null;


        /** Force Delete  */
            public $confirmingForceDelete = false;
            
            public $deletingUserId = null; 
            public function confirmForceDelete($userId)
            {
                $this->confirmingForceDelete = true;
                $this->deletingUserId = $userId;
                $this->passwordConfirm = '';
                $this->passwordError = null;
            }

            public function executeForceDelete()
            {
                if (!Hash::check($this->passwordConfirm, auth()->user()->password)) {
                    $this->passwordError = 'Incorrect password.';
                    return;
                }

                $user = User::withTrashed()->findOrFail($this->deletingUserId);

                // Fetch all projects created by the user, including soft-deleted ones
                $projects = Project::withTrashed()->where('created_by', $user->id)->get();

                foreach ($projects as $project) {
                    // Delete all related data including soft-deleted ones
                    $project->project_subscribers()->withTrashed()->forceDelete();
                    $project->project_documents()->withTrashed()->forceDelete();
                    $project->attachments()->withTrashed()->forceDelete();
                    $project->project_reviewers()->withTrashed()->forceDelete();
                    $project->project_reviews()->withTrashed()->forceDelete();

                    // Force delete the project itself
                    $project->forceDelete();
                }

                // Delete all activity logs created by the user (if soft deletes are used on ActivityLog)
                $activity_logs = ActivityLog::withTrashed()->where('created_by', $user->id)->get();
                foreach ($activity_logs as $activity_log) {
                    $activity_log->forceDelete();
                }

                // Finally, force delete the user
                $user->forceDelete();

                $this->reset(['confirmingForceDelete', 'passwordConfirm', 'deletingUserId', 'passwordError']);
                session()->flash('message', 'User permanently deleted.');
            }

        /** ./ Force Delete */


        /** Recover  */


            public $confirmingRecover = false; 
            public $recoverUserId = null; 

            public function confirmRecover($userId)
            {
                $this->confirmingRecover = true;
                $this->recoverUserId = $userId;
                $this->passwordConfirm = '';
                $this->passwordError = null;
            }


            public function executeRecover()
            {
                if (!Hash::check($this->passwordConfirm, auth()->user()->password)) {
                    $this->passwordError = 'Incorrect password.';
                    return;
                }

                // Restore the user
                $user = User::withTrashed()->findOrFail($this->recoverUserId); 
                $user->restore();

                // Restore all soft-deleted projects by this user
                Project::onlyTrashed()
                    ->where('created_by', $user->id)
                    ->restore();

                // Get the IDs of the user's projects, including restored ones
                $projectIds = Project::where('created_by', $user->id)->pluck('id');

                // Restore related soft-deleted models using whereIn
                ProjectSubscriber::onlyTrashed()
                    ->whereIn('project_id', $projectIds)
                    ->restore();

                ProjectDocument::onlyTrashed()
                    ->whereIn('project_id', $projectIds)
                    ->restore();

                ProjectAttachments::onlyTrashed()
                    ->whereIn('project_id', $projectIds)
                    ->restore();

                ProjectReviewer::onlyTrashed()
                    ->whereIn('project_id', $projectIds)
                    ->restore();

                Review::onlyTrashed()
                    ->whereIn('project_id', $projectIds)
                    ->restore();

                // Restore the user's soft-deleted activity logs
                ActivityLog::onlyTrashed()
                    ->where('created_by', $user->id)
                    ->restore();

                $this->reset([
                    'confirmingRecover',
                    'passwordConfirm',
                    'recoverUserId',
                    'passwordError'
                ]);

                session()->flash('message', 'User and related data restored successfully.');
            }



        /** ./ Recover */



    private function hasConnectedRecords(User $user): bool
    {
        // Check if the user has created any projects
        $projects = Project::where('created_by', $user->id);

        // If the user has any related projects, we check if any of those have connected child records
        foreach ($projects->get() as $project) {
            if (
                $project->project_subscribers()->exists() ||
                $project->project_documents()->exists() ||
                $project->attachments()->exists() ||
                $project->project_reviewers()->exists() ||
                $project->project_reviews()->exists()
            ) {
                return true;
            }
        }

        // If the user is a saved document reviewer
        if (count($user->document_reviewers) > 0 ) {
            return true;
        }


        // If the user is a saved project document reviewer
        if (count($user->reviewed_projects) > 0 ) {
            return true;
        }



        // If the user has at least one project, that's a connection too
        if ($projects->exists()) {
            return true;
        }

        // Check notifications (optional, based on your usage)
        // if ($user->notifications()->exists()) {
        //     return true;
        // }

        return false;
    }



    public function getUsersProperty()
    {
        $users = User::select('users.*');

        // Apply soft delete filter
        switch ($this->user_status_filter) {
            case 'all':
                $users = $users->withTrashed();
                break;
            case 'deactivated':
                $users = $users->onlyTrashed();
                break;
            default:
                // active (default) — no need to modify the query
                break;
        }


        // Search filter
        if (!empty($this->search)) {
            $search = $this->search;
            $users->where(function ($query) use ($search) {
                $query->where('users.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $search . '%');
            });
        }

        // Filter by selected role
        if (!empty($this->selected_role)) {
            $users->when($this->selected_role, function ($query) {
                if ($this->selected_role === 'no_role') {
                    $query->whereDoesntHave('roles');
                } else {
                    $query->whereHas('roles', function ($roleQuery) {
                        $roleQuery->where('id', $this->selected_role);
                    });
                }
            });
        }

        // Filter by role_request
        if (!empty($this->role_request)) {
            $users->where('role_request', $this->role_request);
        }

        // Exclude system access global admin users if current user lacks permission
        $globalAdminUserIds = User::permission('system access global admin')->pluck('id');
        if (!auth()->user() || !auth()->user()->can('system access global admin')) {
            $users->whereNotIn('users.id', $globalAdminUserIds);
        }

        // Sorting
        switch ($this->sort_by) {
            case "Name A - Z":
                $users->orderBy('users.name', 'ASC');
                break;
            case "Name Z - A":
                $users->orderBy('users.name', 'DESC');
                break;
            case "Email A - Z":
                $users->orderBy('users.email', 'ASC');
                break;
            case "Email Z - A":
                $users->orderBy('users.email', 'DESC');
                break;
            case "Latest Added":
                $users->orderBy('users.created_at', 'DESC');
                break;
            case "Oldest Added":
                $users->orderBy('users.created_at', 'ASC');
                break;
            case "Latest Updated":
                $users->orderBy('users.updated_at', 'DESC');
                break;
            case "Oldest Updated":
                $users->orderBy('users.updated_at', 'ASC');
                break;
            default:
                $users->orderBy('users.updated_at', 'DESC');
                break;
        }

        // Set filtered user count
        $this->user_count = $users->count();

        // Paginate results
        return $users->paginate($this->record_count);
    }



    public function render()
    {
        
        // dd($this->users);
        return view('livewire.admin.user.user-list',[
            'users' => $this->users
        ]);




    }
}
