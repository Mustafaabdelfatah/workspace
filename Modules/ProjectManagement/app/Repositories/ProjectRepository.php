<?php

namespace Modules\ProjectManagement\App\Repositories;

use Modules\ProjectManagement\App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Database\Eloquent\Builder;
use Modules\ProjectManagement\App\Traits\Repositories\RepositoryHelperTrait;

class ProjectRepository implements ProjectRepositoryInterface
{
    use RepositoryHelperTrait;

    protected Project $model;

    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    // Basic CRUD operations
    public function find(int $id): ?Project
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Project
    {
        return $this->model->findOrFail($id);
    }

    // Enhanced method with eager loading using trait
    public function findWithRelations(int $id, array $relations = []): ?Project
    {
        $query = $this->model->newQuery();
        return $this->applyEagerLoading($query, $relations)->find($id);
    }

    public function create(array $data): Project
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Project
    {
        $project = $this->findOrFail($id);
        $project->update($data);
        return $project->refresh();
    }

    public function delete(int $id): bool
    {
        $project = $this->findOrFail($id);
        return $project->delete();
    }

    // Enhanced filtered projects with better performance using trait methods
    public function getFilteredProjects(array $filters, int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        return $this->getFilteredProjectsWithRelations($filters, $this->getDefaultProjectRelations(), $perPage, $page);
    }

    public function getFilteredProjectsWithRelations(array $filters, array $relations = [], int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        $query = $this->buildFilteredQuery($filters);

        // Use trait method for eager loading
        $this->applyEagerLoading($query, $relations);

        // Use trait method for ordering
        $this->applyOrdering($query, $filters['order_by'] ?? 'created_at', $filters['order_dir'] ?? 'DESC');

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    // Workspace projects with eager loading using trait
    public function getProjectsByWorkspace(int $workspaceId): Collection
    {
        return $this->getProjectsByWorkspaceWithRelations($workspaceId, $this->getDefaultProjectRelations());
    }

    public function getProjectsByWorkspaceWithRelations(int $workspaceId, array $relations = []): Collection
    {
        $query = $this->buildBaseQuery(['workspace_id' => $workspaceId]);

        // Use trait methods
        $this->applyEagerLoading($query, $relations);
        $this->applyOrdering($query);

        return $query->get();
    }

    // Owner projects with eager loading using trait
    public function getProjectsByOwner(int $ownerId): Collection
    {
        $query = $this->model->where('owner_id', $ownerId);

        // Use trait methods
        $this->applyEagerLoading($query, ['workspace', 'manager']);
        $this->applyOrdering($query);

        return $query->get();
    }

    // Sub-projects with eager loading using trait
    public function getSubProjects(int $parentProjectId): Collection
    {
        return $this->getSubProjectsWithRelations($parentProjectId, $this->getDefaultProjectRelations());
    }

    public function getSubProjectsWithRelations(int $parentProjectId, array $relations = []): Collection
    {
        $query = $this->model->where('parent_project_id', $parentProjectId);

        // Use trait methods
        $this->applyEagerLoading($query, $relations);
        $this->applyOrdering($query);

        return $query->get();
    }

    public function generateUniqueCode(int $workspaceId): string
    {
        return Project::generateCode($workspaceId);
    }

    // Performance optimized query builder methods
    public function getProjectStats(int $workspaceId): Collection
    {
        return $this->model
            ->selectRaw('status, COUNT(*) as count, AVG(DATEDIFF(end_date, start_date)) as avg_days')
            ->where('workspace_id', $workspaceId)
            ->groupBy('status')
            ->get();
    }

    public function getProjectsCountByStatus(int $workspaceId): array
    {
        return $this->model
            ->where('workspace_id', $workspaceId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    // Private helper method using trait's buildBaseQuery
    private function buildFilteredQuery(array $filters): Builder
    {
        return app(Pipeline::class)
            ->send($this->buildBaseQuery($filters))
            ->through($this->getFilterPipeline($filters))
            ->thenReturn();
    }

    private function getFilterPipeline(array $filters): array
    {
        return [
            \Modules\ProjectManagement\App\Pipelines\Filters\WorkspaceFilter::class,
            \Modules\ProjectManagement\App\Pipelines\Filters\StatusFilter::class,
            \Modules\ProjectManagement\App\Pipelines\Filters\ProjectTypeFilter::class,
            \Modules\ProjectManagement\App\Pipelines\Filters\BuildingTypeFilter::class,
            \Modules\ProjectManagement\App\Pipelines\Filters\OwnerFilter::class,
            \Modules\ProjectManagement\App\Pipelines\Filters\ManagerFilter::class,
            \Modules\ProjectManagement\App\Pipelines\Filters\ParentProjectFilter::class,
            \Modules\ProjectManagement\App\Pipelines\Filters\CoordinatesFilter::class,
            \Modules\ProjectManagement\App\Pipelines\Filters\SearchFilter::class,
            \Modules\ProjectManagement\App\Pipelines\Filters\DateRangeFilter::class,
        ];
    }

    private function getDefaultProjectRelations(): array
    {
        return [
            'workspace',
            'owner',
            'manager',
            'parentProject',
            'invitations.invitedUser',
            'invitations.userGroup',
            'members'
        ];
    }

    // Enhanced method to get projects with invitations
    public function getProjectsWithInvitations(int $workspaceId, array $filters = []): Collection
    {
        $query = $this->buildBaseQuery(['workspace_id' => $workspaceId]);

        $this->applyEagerLoading($query, [
            'invitations' => function($q) {
                $q->pending();
            },
            'workspace',
            'owner',
            'manager'
        ]);

        return $query->get();
    }

    // Get project by building type
    public function getProjectsByBuildingType(int $workspaceId, string $buildingType): Collection
    {
        $query = $this->model->where('workspace_id', $workspaceId)
                            ->where('building_type', $buildingType);

        $this->applyEagerLoading($query, $this->getDefaultProjectRelations());
        $this->applyOrdering($query);

        return $query->get();
    }

    // Get projects that need workspace details validation
    public function getProjectsNeedingWorkspaceValidation(int $workspaceId): Collection
    {
        $query = $this->model->where('workspace_id', $workspaceId)
                            ->whereJsonDoesntHave('settings->workspace_validated');

        $this->applyEagerLoading($query, ['workspace', 'owner']);

        return $query->get();
    }

    // Mark project as workspace validated
    public function markWorkspaceValidated(int $projectId): bool
    {
        $project = $this->findOrFail($projectId);
        $settings = $project->settings ?? [];
        $settings['workspace_validated'] = true;
        $settings['workspace_validated_at'] = now()->toISOString();

        return $project->update(['settings' => $settings]);
    }
}
