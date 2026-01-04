<?php 
namespace App\Helpers\ActivityLogHelpers;
use Carbon\Carbon;
use App\Models\User;  
use Illuminate\Support\Str;
use App\Models\ActivityLog;  
use App\Models\ProjectTimer;  
use App\Events\ProjectTimer\TimeSettingsUpdated;
 
class ProjectTimerLogHelper
{



    /**
     * Send a system notification event to specific groups of users.
     *
     * @param  int           $projectTimerId the id of the project timer
     * @param  string|array  $types        'admin', 'reviewer', 'user', 'guest', 'all' or a combination. 
     * @param  array         $customIds    Extra user IDs that should also be notified.
     * @param  string        $notification_type        Notification type: success|error|info|warning...
     * @param  array         $excludedCustomIds    Extra user IDs that should also be notified.
     * @param  string        $link          Link to the page connected to the notification 
     * @return \Illuminate\Support\Collection  The user IDs that were targeted.
     */
    public static function sendEmailUpdateEvent(
        int $projectTimerId,
        string|array $role_types = 'all', 
        array $customIds = [], 
        string $notification_type = 'info',
        array $excludedCustomIds = [],
        string $link = '', 
    ): void //: Collection 
    {

        // dd("Here");

        // Normalize to array
        $role_types = is_array($role_types) ? $role_types : [$role_types];

        // Start with empty collection of user IDs
        $userIds = collect();

        // If "all" is requested, just get everyone and ignore other types,
        // but we’ll still merge customIds later.
        if (in_array('all', $role_types, true)) {
            $userIds = User::pluck('id');
        } else {
            // Map from type (admin/user/reviewer) to permission name
            $permissionMap = [
                'admin'    => 'system access admin',
                'global_admin'    => 'system access global admin',
                'reviewer' => 'system access reviewer',
                'user'     => 'system access user',
            ];

            // Collect permissions we need to check based on the requested types
            $permissionsToCheck = [];

            foreach ($permissionMap as $type => $permissionName) {
                if (in_array($type, $role_types, true)) {
                    $permissionsToCheck[] = $permissionName;
                }
            }


            // dd($permissionsToCheck);

            // If admin/user/reviewer types are requested, get users by permissions
            if (! empty($permissionsToCheck)) {
                // dd(User::permission($permissionsToCheck)->pluck('name'));

                $idsWithPermissions = User::permission($permissionsToCheck)->pluck('id');
                $userIds = $userIds->merge($idsWithPermissions);
            }

            // Handle "guest" → users with NO role AND NO permissions
            if (in_array('guest', $role_types, true)) {
                $guestIds = User::doesntHave('roles')
                    ->doesntHave('permissions')
                    ->pluck('id');

                $userIds = $userIds->merge($guestIds);
            }
        }

        // Merge any custom user IDs passed in
        if (! empty($customIds)) {
            $userIds = $userIds->merge($customIds);
        }

        // Exclude ids passed in the parameter
        if(!empty($excludedCustomIds)){
            // $excludeIds can be a single array or an array of arrays
            $excludeList = collect($excludedCustomIds)->flatten()->unique();

            // dd($excludeList );

            $filteredCustomIds = collect($userIds)
                ->diff($excludeList);   // remove excluded ones

            // dd($filteredCustomIds);
            
            $userIds = $filteredCustomIds;
            // dd($userIds);

        }

        // Remove duplicates and reindex
        $userIds = $userIds->unique()->values();

        // dd($userIds);



        //get the project timer instance
        $projectTimer = ProjectTimer::find($projectTimerId);
        $authId = $projectTimer->updated_by; // the updater is the initiator of hte changes made in the project time settings


        // dd($notification_type);
        // // 🔔 Dispatch your per-user email event
        foreach ($userIds as $userId) {

            $targetUserEmailForRole = ProjectTimerLogHelper::primaryEmailRolePermission($userId); // function to get the target role
            // dd($userId);
            // dd($targetUserEmailForRole);

            // send the TimeSettingsUpdated that will trigger the event to send the email notifications
            event(new TimeSettingsUpdated(
                $projectTimerId,
                $userId, 
                $targetUserEmailForRole,
                $authId,
                true,
                
            ));
        }
  



        // dd($projectTimer);

        // return $userIds;
    } 



    public static function primaryEmailRolePermission($userId): string
    {
        $user = User::with('roles.permissions')->find($userId);

        if (!$user) {
            return 'user';
        }

        // Collect ALL role-based permission names (lowercase)
        $permissions = $user->roles
            ->flatMap(fn ($role) => $role->permissions)
            ->pluck('name')
            ->map(fn ($name) => Str::lower($name))
            ->unique()
            ->values();

        // Priority order (top-down)
        if ($permissions->contains('system access global admin')) {
            return 'admin';
        }

        if ($permissions->contains('system access admin')) {
            return 'admin';
        }

        if ($permissions->contains('system access reviewer')) {
            return 'reviewer';
        }

        return 'user';
    }


    
    /**
     * Generate a ActivityLog for Role based on event
     * @param  string       $event          'created', 'updated', 'deleted', 
     * @param  int          $project_timerId         project_timer id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function logActivity(string $event = null, int $authId){
 
        // get the user that initiated the event
        $authUser = User::find($authId); 
 
        // get the message
        $message = ProjectTimerLogHelper::getActivityMessage($event,$authId);
        
        // save the activity log
        ActivityLog::create([
            'created_by' => $authUser->id,
            'log_username' => $authUser->name,
            'log_action' =>  $message, 
        ]); 
    }

    /**
     * Generate a ActivityLog message || This is also used in messages returned 
     * @param  string       $event          'created', 'updated', 'deleted', 
     * @param  int          $project_timerId         project_timer id
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     */
    public static function getActivityMessage(string $event, int $authId): string{
 
        // get the user that initiated the event
        $authUser = User::find($authId);  
        $authName = $authUser->name ?? 'Auth unnamed'; 
 
        // return message based on the event
        return match($event) { 
            // event
            'updated' => "Project time settings updated successfully by '{$authName}'.",

            // error messages
            'response_duration' => 'Response duration settings are not yet configured.',
            'project_submission_times' =>  'Project submission times are not set.',
            'no_reviewers' =>  'No reviewers have been set.',
            'no_document_types' =>  'Document types have not been added.',


            default => "Project time settings updated successfully by '{$authName}'."
        };
 
    } 

 
     /**
     * get route based on event 
     * @param  string       $event          'new-user-verification-request'
     * @param  int          $project_timerId         project_timer id optional
     * @param  int          $authId         auth id 
     * @return void         void            Not required to return value
     * */

    public static function getRoute(string $event): string{
 

        // return message based on the event
        return match($event) {   
            default =>  route('project_timer.index')
        };

    }


}
