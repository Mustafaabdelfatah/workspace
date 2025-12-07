<?php

namespace Modules\ProjectManagement\App\Repositories;

use Modules\ProjectManagement\App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pipeline\Pipeline;

class ProjectRepository implements ProjectRepositoryInterface
{
    protected $model;

    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    public function find(int $id): ?Project
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id): Project
    {
        return $this->model->findOrFail($id);
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

    public function getFilteredProjects(array $filters, int $perPage = 20, int $page = 1): LengthAwarePaginator
    {
        $query = app(Pipeline::class)
            ->send($this->model->newQuery())
            ->through($this->getFilterPipeline($filters))
            ->thenReturn();

        return $query->with(['workspace', 'owner', 'manager', 'parentProject'])
                    ->orderBy($filters['order_by'] ?? 'created_at', $filters['order_dir'] ?? 'DESC')
                    ->paginate($perPage, ['*'], 'page', $page);
    }

    public function getProjectsByWorkspace(int $workspaceId): Collection
    {
        return $this->model->where('workspace_id', $workspaceId)
                          ->with(['owner', 'manager'])
                          ->get();
    }

    public function getProjectsByOwner(int $ownerId): Collection
    {
        return $this->model->where('owner_id', $ownerId)
                          ->with(['workspace', 'manager'])
                          ->get();
    }

    public function getSubProjects(int $parentProjectId): Collection
    {
        return $this->model->where('parent_project_id', $parentProjectId)
                          ->with(['owner', 'manager'])
                          ->get();
    }

    public function generateUniqueCode(int $workspaceId): string
    {
        return Project::generateCode($workspaceId);
    }

    /**
     * Get the filter pipeline classes
     */
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
            // \Modules\ProjectManagement\App\Pipelines\Filters\CompanyFilter::class, // Uncomment when company module is ready
            // \Modules\ProjectManagement\App\Pipelines\Filters\CompanyPositionFilter::class, // Uncomment when company module is ready
            \Modules\ProjectManagement\App\Pipelines\Filters\CoordinatesFilter::class,
            \Modules\ProjectManagement\App\Pipelines\Filters\SearchFilter::class,
            \Modules\ProjectManagement\App\Pipelines\Filters\UserPermissionFilter::class,
        ];
    }
}
