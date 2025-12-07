<?php

namespace Modules\ProjectManagement\App\Repositories;

use Modules\ProjectManagement\App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface
{
    public function find(int $id): ?Project;
    public function findOrFail(int $id): Project;
    public function create(array $data): Project;
    public function update(int $id, array $data): Project;
    public function delete(int $id): bool;
    public function getFilteredProjects(array $filters, int $perPage = 20, int $page = 1): LengthAwarePaginator;
    public function getProjectsByWorkspace(int $workspaceId): Collection;
    public function getProjectsByOwner(int $ownerId): Collection;
    public function getSubProjects(int $parentProjectId): Collection;
    public function generateUniqueCode(int $workspaceId): string;
}
