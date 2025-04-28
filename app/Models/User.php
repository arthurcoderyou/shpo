<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use RealRashid\SweetAlert\Facades\Alert;
use App\Notifications\CustomVerifyEmail; 
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    // find CustomVerifyEmail because it is the custom notification for the verify email
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }



    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_request',
        'otp_code',
        'otp_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * Get all of the projects for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'created_by', 'id');
    }


    // get reviewed projects 




    /**
     * Projects that this user created.
     */
    public function created_projects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    /**
     * Projects that this user is a reviewer of.
     */
    public function reviewed_projects()
    {
        return $this->belongsToMany(Project::class, 'project_reviewers', 'user_id', 'project_id');
    }


    static public function countUsers($selected_role = null,$role_request = null){

        $users = User::select('users.*');

         
        
        if(!empty($selected_role)){
            $users = $users->when($selected_role, function ($query) use ($selected_role) {
                // $query->whereHas('roles', function ($roleQuery) {
                //     $roleQuery->where('id', $selected_role);
                // });
    
                if ($selected_role === 'no_role') {
                    // Users without roles
                    $query->whereDoesntHave('roles');
                } else {
                    // Users with the selected role
                    $query->whereHas('roles', function ($roleQuery) use ($selected_role) {
                        $roleQuery->where('id', $selected_role);
                    });
                }
            });

 
        }
        

        if(!empty($role_request)){
            $users = $users->where('role_request',$role_request);
            
        }


        return $users->count();

    }



    static public function permission_verification($permission){
        // Permission verification 
            $user = Auth::user();
            // Check if the user has the role "DSI God Admin" OR the permission "activity log list view"
            if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo($permission))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->back()->withInput()->withErrors(['error' => 'Unauthorized Access'])
                    ?: redirect()->route('dashboard');
            }
        // ./ Permission verification

    }









}
