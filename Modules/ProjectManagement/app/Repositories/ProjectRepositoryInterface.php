<?php

namespace Modules\ProjectManagement\App\Repositories;

use Modules\ProjectManagement\App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface
{
    // Basic CRUD operations
    public function find(int $id): ?Project;
    public function findOrFail(int $id): Project;
    public function create(array $data): Project;
    public function update(int $id, array $data): Project;
    public function delete(int $id): bool;

    // Enhanced methods with eager loading support
    public function findWithRelations(int $id, array $relations = []): ?Project;
    public function getFilteredProjects(array $filters, int $perPage = 20, int $page = 1): LengthAwarePaginator;
    public function getFilteredProjectsWithRelations(array $filters, array $relations = [], int $perPage = 20, int $page = 1): LengthAwarePaginator;
    public function getProjectsByWorkspace(int $workspaceId): Collection;
    public function getProjectsByWorkspaceWithRelations(int $workspaceId, array $relations = []): Collection;
    public function getProjectsByOwner(int $ownerId): Collection;
    public function getSubProjects(int $parentProjectId): Collection;
    public function getSubProjectsWithRelations(int $parentProjectId, array $relations = []): Collection;
    public function generateUniqueCode(int $workspaceId): string;
}
