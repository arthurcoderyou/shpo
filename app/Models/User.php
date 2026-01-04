<?php

namespace App\Models;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use RealRashid\SweetAlert\Facades\Alert;
use App\Notifications\CustomVerifyEmail; 
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected static function booted()
    {

        parent::boot();
        
        // static::created(function ($user) {

        //     $authId = auth::check() ? auth()->user()->id : $user->id;

        //     event(new  \App\Events\UserCreated($user,$authId)); 
        //     // \App\Services\CacheService::updateUserStats(); 
        // });

        // static::updated(function ($user) {
        //     event(new  \App\Events\UserUpdated($user, auth()->check() ?? auth()->user()->id )); 
        //     // \App\Services\CacheService::updateUserStats();
        // });

        // static::deleted(function ($user) {
        //     event(new  \App\Events\UserDeleted( $user, auth()->user()->id ));
        //     // \App\Services\CacheService::updateUserStats();
        // });
    }



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
        'address',
        'company',
        'phone_number',
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


    // public function notifications()
    // {
    //     return $this->morphMany(\App\Models\Notification::class, 'notifiable')->orderBy('created_at', 'desc');
    // }

     /**
     * Get all of the activity logs for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activity_logs()
    {
        return $this->hasMany(ActivityLog::class, 'created_by', 'id');
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
     * Document Reviewer that this user is part of .
     */
    public function document_reviewers()
    {
        return $this->hasMany(Reviewer::class, 'user_id');
    }


    /**
     * Projects that this user is a reviewer of.
     */
    public function reviewed_projects()
    {
        return $this->belongsToMany(Project::class, 'project_reviewers', 'user_id', 'project_id');
    }


    static public function countUsers($selected_role = null, $role_request = null, $noRoleOnly = false, $with_dsi_admin = false)
    {
        $users = User::select('users.*');

        // Filter by role if provided
        if (!empty($selected_role)) {
            $users->when($selected_role, function ($query) use ($selected_role) {
                if ($selected_role === 'no_role') {
                    $query->whereDoesntHave('roles');
                } else {
                    $query->whereHas('roles', function ($roleQuery) use ($selected_role) {
                        $roleQuery->where('id', $selected_role);
                    });
                }
            });
        }

        // Filter for users with NO roles (override parameter)
        if ($noRoleOnly) {
            $users = $users->whereDoesntHave('roles');
        }

        // Get user IDs with 'system access global admin' permission
        $globalAdminUserIds = User::permission('system access global admin')->pluck('id');

        // If logged-in user lacks permission, exclude global admin users
        // if (!auth()->user() || !auth()->user()->can('system access global admin')) {

        if($with_dsi_admin == false){
            $users = $users->whereNotIn('users.id', $globalAdminUserIds);
        }

        // Optional: Filter by role_request
        if (!empty($role_request)) {
            $users = $users->where('role_request', $role_request);
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



    static public function getRoleRequestByEmail($email)
    {
        $email = strtolower($email);

        $reviewerDomains = [
            '@gmail.com',
            '@khlgassociates.com',
        ];

        foreach ($reviewerDomains as $pattern) {
            if (str_ends_with($email, $pattern)) {
                return 'reviewer';
            }
        }

        return 'user';
    }




    public static function generateInitials($name)
    {
        // Trim extra spaces
        $name = trim($name);

        // Split into words
        $parts = preg_split('/\s+/', $name);

        // If only one word → use first two letters
        if (count($parts) === 1) {
            return strtoupper(substr($parts[0], 0, 2));
        }

        // If multiple words → use first letter of each
        $initials = collect($parts)->map(
            fn($part) => strtoupper(substr($part, 0, 1))
        );

        return $initials->implode('');
    }

}
