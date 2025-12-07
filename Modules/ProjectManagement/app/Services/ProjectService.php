<?php

namespace Modules\ProjectManagement\App\Services;

use Modules\ProjectManagement\App\Repositories\ProjectRepositoryInterface;
use Modules\ProjectManagement\App\Models\Project;
use Modules\ProjectManagement\App\Enums\ProjectStatusEnum;
use Modules\ProjectManagement\App\Enums\ProjectTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectService
{
    protected $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * Create a new project
     */
    public function createProject(array $data): Project
    {
        $user = Auth::user();

        // Set default values
        $projectData = array_merge([
            'owner_id' => $user->id,
            'status' => ProjectStatusEnum::PLANNING->value,
            'project_type' => ProjectTypeEnum::RESIDENTIAL->value,
            'area_unit' => 'm²',
        ], $data);

        // Generate unique code
        $projectData['code'] = $this->projectRepository->generateUniqueCode($data['workspace_id']);

        return DB::transaction(function () use ($projectData) {
            $project = $this->projectRepository->create($projectData);

            // Add owner as a member automatically
            $project->addMember(Auth::user(), 'owner');

            return $project->load(['workspace', 'owner', 'manager', 'parentProject']);
        });
    }

    /**
     * Update an existing project
     */
    public function updateProject(int $id, array $data): Project
    {
        $project = $this->projectRepository->findOrFail($id);
        $user = Auth::user();

        // Check permissions
        if (!$this->canUpdateProject($project, $user)) {
            throw new \Exception('You do not have permission to update this project.');
        }

        // Filter out null values
        $updateData = array_filter($data, function($value) {
            return $value !== null;
        });

        return DB::transaction(function () use ($id, $updateData) {
            return $this->projectRepository->update($id, $updateData)
                                          ->load(['workspace', 'owner', 'manager', 'parentProject']);
        });
    }

    /**
     * Delete a project
     */
    public function deleteProject(int $id): bool
    {
        $project = $this->projectRepository->findOrFail($id);
        $user = Auth::user();

        // Check permissions
        if (!$this->canDeleteProject($project, $user)) {
            throw new \Exception('You do not have permission to delete this project.');
        }

        return $this->projectRepository->delete($id);
    }

    /**
     * Get filtered projects with pagination
     */
    public function getFilteredProjects(array $filters, int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        return $this->projectRepository->getFilteredProjects($filters, $perPage, $page);
    }

    /**
     * Get project by ID
     */
    public function getProjectById(int $id): ?Project
    {
        return $this->projectRepository->find($id);
    }

    /**
     * Get projects by workspace
     */
    public function getProjectsByWorkspace(int $workspaceId): Collection
    {
        return $this->projectRepository->getProjectsByWorkspace($workspaceId);
    }

    /**
     * Get sub-projects of a parent project
     */
    public function getSubProjects(int $parentProjectId): Collection
    {
        return $this->projectRepository->getSubProjects($parentProjectId);
    }

    /**
     * Add member to project
     */
    public function addMemberToProject(int $projectId, int $userId, string $role = 'member'): void
    {
        $project = $this->projectRepository->findOrFail($projectId);
        $user = Auth::user();

        if (!$this->canManageProjectMembers($project, $user)) {
            throw new \Exception('You do not have permission to manage project members.');
        }

        $memberUser = \Modules\Core\Models\User::findOrFail($userId);
        $project->addMember($memberUser, $role);
    }

    /**
     * Remove member from project
     */
    public function removeMemberFromProject(int $projectId, int $userId): void
    {
        $project = $this->projectRepository->findOrFail($projectId);
        $user = Auth::user();

        if (!$this->canManageProjectMembers($project, $user)) {
            throw new \Exception('You do not have permission to manage project members.');
        }

        $memberUser = \Modules\Core\Models\User::findOrFail($userId);
        $project->removeMember($memberUser);
    }

    /**
     * Check if user can update project
     */
    private function canUpdateProject(Project $project, $user): bool
    {
        return $user->is_admin ||
               $project->owner_id === $user->id ||
               $project->manager_id === $user->id;
    }

    /**
     * Check if user can delete project
     */
    private function canDeleteProject(Project $project, $user): bool
    {
        return $user->is_admin || $project->owner_id === $user->id;
    }

    /**
     * Check if user can manage project members
     */
    private function canManageProjectMembers(Project $project, $user): bool
    {
        return $user->is_admin ||
               $project->owner_id === $user->id ||
               $project->manager_id === $user->id;
    }
}
