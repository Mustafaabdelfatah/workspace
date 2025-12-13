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
use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\User;
use Modules\ProjectManagement\App\Traits\Services\ServiceHelperTrait;
use Modules\ProjectManagement\App\Traits\Services\ProjectPermissionTrait;

class ProjectService
{
    use ServiceHelperTrait, ProjectPermissionTrait;

    protected ProjectRepositoryInterface $projectRepository;

    // Define relations to eager load for better performance
    protected array $defaultRelations = [
        'workspace',
        'owner',
        'manager',
        'parentProject'
    ];

    protected array $detailedRelations = [
        'workspace',
        'owner',
        'manager',
        'parentProject',
        'subProjects',
        'members'
    ];

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    /**
     * Create a new project with optimized loading
     */
    public function createProject(array $data): Project
    {
        $user = $this->getCurrentUser();
        $projectData = $this->prepareProjectData($data, $user);

        return DB::transaction(function () use ($projectData, $user) {
            $project = $this->projectRepository->create($projectData);
            $this->addOwnerAsMember($project, $user);

            // Use trait method for cache clearing
            $this->clearProjectCaches($project->workspace_id);

            return $project->load($this->defaultRelations);
        });
    }

    /**
     * Update an existing project with optimized queries
     */
    public function updateProject(int $id, array $data): Project
    {
        $user = $this->getCurrentUser();

        // Use eager loading to get project with relations in one query
        $project = $this->projectRepository->findWithRelations($id, ['owner', 'workspace']);

        // Use trait method for permission validation
        $this->validateUpdatePermission($project, $user);

        $updateData = $this->removeNullValues($data);

        return DB::transaction(function () use ($project, $updateData) {
            $updatedProject = $this->projectRepository->update($project->id, $updateData);

            // Use trait method for cache clearing
            $this->clearProjectCaches($project->workspace_id, $project->id);

            return $updatedProject->load($this->defaultRelations);
        });
    }

    /**
     * Delete a project with permission checks
     */
    public function deleteProject(int $id): bool
    {
        $user = $this->getCurrentUser();
        $project = $this->projectRepository->findWithRelations($id, ['owner', 'workspace']);

        // Use trait method for permission validation
        $this->validateDeletePermission($project, $user);

        return DB::transaction(function () use ($project) {
            // Use trait method for cache clearing
            $this->clearProjectCaches($project->workspace_id, $project->id);

            return $this->projectRepository->delete($project->id);
        });
    }

    /**
     * Get project by ID with optimized loading
     */
    public function getProjectById(int $id, bool $detailed = false): ?Project
    {
        $relations = $detailed ? $this->detailedRelations : $this->defaultRelations;

        return Cache::remember(
            $this->generateCacheKey("project", ['id' => $id, 'relations' => $relations]),
            $this->getCacheTtl(),
            fn() => $this->projectRepository->findWithRelations($id, $relations)
        );
    }

    /**
     * Get projects by workspace with caching and eager loading
     */
    public function getProjectsByWorkspace(int $workspaceId, bool $withDetails = false): Collection
    {
        $relations = $withDetails ? $this->detailedRelations : $this->defaultRelations;

        return Cache::remember(
            $this->generateCacheKey("workspace_projects", ['workspace_id' => $workspaceId, 'relations' => $relations]),
            $this->getCacheTtl(),
            fn() => $this->projectRepository->getProjectsByWorkspaceWithRelations($workspaceId, $relations)
        );
    }

    /**
     * Get filtered projects with pagination and eager loading
     */
    public function getFilteredProjects(array $filters, int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        return $this->projectRepository->getFilteredProjectsWithRelations($filters, $this->defaultRelations, $perPage, $page);
    }

    /**
     * Get sub-projects with eager loading
     */
    public function getSubProjects(int $parentProjectId): Collection
    {
        return Cache::remember(
            $this->generateCacheKey("sub_projects", ['parent_id' => $parentProjectId]),
            $this->getCacheTtl(),
            fn() => $this->projectRepository->getSubProjectsWithRelations($parentProjectId, $this->defaultRelations)
        );
    }

    /**
     * Change project status efficiently
     */
    public function changeProjectStatus(int $projectId, string $status): Project
    {
        $user = $this->getCurrentUser();
        $project = $this->projectRepository->findWithRelations($projectId, ['owner']);

        // Use trait method for permission validation
        $this->validateUpdatePermission($project, $user);

        return DB::transaction(function () use ($project, $status) {
            $project->update(['status' => $status]);
            $this->clearProjectCaches($project->workspace_id, $project->id);

            return $project->fresh($this->defaultRelations);
        });
    }

    // Private helper methods for cleaner code
    private function prepareProjectData(array $data, User $user): array
    {
        return array_merge([
            'owner_id' => $user->id,
            'status' => ProjectStatusEnum::PLANNING->value,
            'project_type' => ProjectTypeEnum::RESIDENTIAL->value,
            'area_unit' => 'm²',
        ], $data, [
            'code' => $this->projectRepository->generateUniqueCode($data['workspace_id'])
        ]);
    }

    private function addOwnerAsMember(Project $project, User $user): void
    {
        $project->addMember($user, 'owner');
    }
}
