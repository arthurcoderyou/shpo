<?php 
namespace App\Helpers\SystemNotificationHelpers;
use App\Models\User; 
use App\Events\System\SystemEvent;
use Illuminate\Support\Collection;
 
class SystemNotificationHelper
{
 
 
    /**
     * Send a system notification event to specific groups of users.
     *
     * @param  string|array  $types        'admin', 'reviewer', 'user', 'guest', 'all' or a combination.
     * @param  string        $message      The notification message.
     * @param  array         $customIds    Extra user IDs that should also be notified.
     * @param  string        $type        Notification type: success|error|info|warning...
     * @param  array         $excludedCustomIds    Extra user IDs that should also be notified.
     * @param  string        $link          Link to the page connected to the notification
     * @param  array         $customIdsNotifiedWithoutPopup    Extra user IDs that should also be notified without the popup notification. 
     * @return \Illuminate\Support\Collection  The user IDs that were targeted.
     */
    public static function sendSystemNotificationEvent(
        string|array $role_types = 'all',
        string $message = '',
        array $customIds = [],
        string $notification_type = 'info',
        array $excludedCustomIds = [],
        string $link = '',
        array $customIdsNotifiedWithoutPopup = [],  

        
    ): Collection {
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


        // dd($notification_type);
        // // 🔔 Dispatch your per-user notification event
        foreach ($userIds as $userId) {
            // You can adapt this to your actual event
            event(new SystemEvent(
                targetUserId: $userId,
                message: $message,
                notification_type: $notification_type,
                link: $link
            ));
        }

         

        // Notify custom ids that are notified without popup
        if(!empty($customIdsNotifiedWithoutPopup)){
            // $excludeIds can be a single array or an array of arrays
            $customIdsNotifiedWithoutPopup = collect($customIdsNotifiedWithoutPopup)->flatten()->unique(); 
            

            foreach ($customIdsNotifiedWithoutPopup as $customId) {
                // You can adapt this to your actual event
                event(new SystemEvent(
                    targetUserId: $customId,
                    message: $message,
                    notification_type: $notification_type,
                    link: $link,
                    show_notification_popup: false, // this disables the notification popup but still gets the notification 
                ));
            } 
        }

          

        // dd($userIds);

        return $userIds;
    } 





    /**
     * Get user ids based on role type 
     * 
     * @param  string|array  $role_types        'admin', 'reviewer', 'user', 'guest', 'all' or a combination.
     * @return \Illuminate\Support\Collection  The user IDs that were targeted.
     */
    public static function getUserIdsBasedOnRoles(
        string|array $role_types = 'all',
    ): Collection{
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

        return $userIds;
    }


 

}
