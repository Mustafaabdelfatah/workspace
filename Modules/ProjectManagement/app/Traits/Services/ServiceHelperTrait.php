<?php

namespace Modules\ProjectManagement\App\Traits\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Models\User;

trait ServiceHelperTrait
{
    /**
     * Get the currently authenticated user
     */
    protected function getCurrentUser(): User
    {
        return Auth::user();
    }

    /**
     * Clear project-related caches
     */
    protected function clearProjectCaches(int $workspaceId, ?int $projectId = null): void
    {
        $patterns = [
            "workspace_projects:{$workspaceId}:*",
            "project_stats:{$workspaceId}:*"
        ];

        if ($projectId) {
            $patterns[] = "project:{$projectId}:*";
            $patterns[] = "sub_projects:{$projectId}";
        }

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Filter out null values from array
     */
    protected function removeNullValues(array $data): array
    {
        return array_filter($data, fn($value) => $value !== null);
    }

    /**
     * Generate cache key with TTL
     */
    protected function generateCacheKey(string $prefix, array $params = []): string
    {
        $key = $prefix;
        if (!empty($params)) {
            $key .= ':' . md5(serialize($params));
        }
        return $key;
    }

    /**
     * Get cache TTL from config
     */
    protected function getCacheTtl(): int
    {
        return config('cache.ttl', 3600);
    }
}
