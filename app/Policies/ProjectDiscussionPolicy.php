<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectDiscussionPolicy
{
    /**
     * Determine if the user can participate in the project discussion (post or reply).
     */
    public function canParticipate(User $user, Project $project): bool
    {
        // Check if the user has a privileged role
        if ($user->hasRole(['DSI God Admin', 'Admin'])) {
            return true;
        }

        // Check if the user is the creator of the project
        if ($project->created_by === $user->id) {
            return true;
        }

        // Check if the user is a reviewer of the project
        return $project->project_reviewers()
            ->where('user_id', $user->id)
            ->exists();
    }
}
