<?php

namespace Modules\ProjectManagement\App\Traits\Services;

use Modules\ProjectManagement\App\Models\Project;
use Modules\Core\Models\User;

trait ProjectPermissionTrait
{
    /**
     * Check if user can update project
     */
    protected function canUpdateProject(Project $project, User $user): bool
    {
        return $user->is_admin ||
               $project->owner_id === $user->id ||
               $project->manager_id === $user->id;
    }

    /**
     * Check if user can delete project
     */
    protected function canDeleteProject(Project $project, User $user): bool
    {
        return $user->is_admin || $project->owner_id === $user->id;
    }

    /**
     * Check if user can manage project members
     */
    protected function canManageProjectMembers(Project $project, User $user): bool
    {
        return $user->is_admin ||
               $project->owner_id === $user->id ||
               $project->manager_id === $user->id;
    }

    /**
     * Check if user has access to view project
     */
    protected function hasProjectAccess(Project $project, User $user): bool
    {
        return $user->is_admin ||
               $project->owner_id === $user->id ||
               $project->manager_id === $user->id ||
               $project->hasMember($user);
    }

    /**
     * Validate permission and throw exception if not allowed
     */
    protected function validateUpdatePermission(Project $project, User $user): void
    {
        if (!$this->canUpdateProject($project, $user)) {
            throw new \Exception('You do not have permission to update this project.');
        }
    }

    /**
     * Validate delete permission and throw exception if not allowed
     */
    protected function validateDeletePermission(Project $project, User $user): void
    {
        if (!$this->canDeleteProject($project, $user)) {
            throw new \Exception('You do not have permission to delete this project.');
        }
    }

    /**
     * Validate access permission and throw exception if not allowed
     */
    protected function validateProjectAccess(Project $project, User $user): void
    {
        if (!$this->hasProjectAccess($project, $user)) {
            throw new \Exception('You do not have permission to access this project.');
        }
    }
}
