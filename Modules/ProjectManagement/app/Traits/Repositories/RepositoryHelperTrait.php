<?php

namespace Modules\ProjectManagement\App\Traits\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait RepositoryHelperTrait
{
    /**
     * Apply eager loading to query if relations provided
     */
    protected function applyEagerLoading(Builder $query, array $relations = []): Builder
    {
        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query;
    }

    /**
     * Apply ordering to query
     */
    protected function applyOrdering(Builder $query, string $orderBy = 'created_at', string $orderDir = 'DESC'): Builder
    {
        return $query->orderBy($orderBy, $orderDir);
    }

    /**
     * Get default project relations for basic loading
     */
    protected function getDefaultProjectRelations(): array
    {
        return ['workspace', 'owner', 'manager', 'parentProject'];
    }

    /**
     * Get detailed project relations for comprehensive loading
     */
    protected function getDetailedProjectRelations(): array
    {
        return [
            'workspace',
            'owner',
            'manager',
            'parentProject',
            'subProjects',
            'members'
        ];
    }

    /**
     * Build optimized query with common filters
     */
    protected function buildBaseQuery(array $filters = []): Builder
    {
        $query = $this->model->newQuery();

        // Apply workspace filter if provided
        if (isset($filters['workspace_id'])) {
            $query->where('workspace_id', $filters['workspace_id']);
        }

        return $query;
    }
}
