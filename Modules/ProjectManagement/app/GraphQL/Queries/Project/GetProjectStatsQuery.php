<?php
namespace Modules\ProjectManagement\App\GraphQL\Queries\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Repositories\ProjectRepositoryInterface;
use Modules\ProjectManagement\App\Traits\Services\ServiceHelperTrait;
use Illuminate\Support\Facades\Cache;

class GetProjectStatsQuery extends Query
{
    use ServiceHelperTrait;

    protected $attributes = [
        'name' => 'projectStats',
    ];

    public function __construct(
        private ProjectRepositoryInterface $projectRepository
    ) {}

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('ProjectStats'));
    }

    public function args(): array
    {
        return [
            'workspace_id' => [
                'name' => 'workspace_id',
                'type' => Type::int(),
            ],
        ];
    }

    public function resolve($root, array $args): array
    {
        $workspaceId = $args['workspace_id'] ?? null;
        $cacheKey = $this->generateCacheKey("project_stats", ['workspace_id' => $workspaceId]);

        return Cache::remember($cacheKey, $this->getCacheTtl(), function () use ($workspaceId) {
            return $this->getOptimizedStats($workspaceId);
        });
    }

    private function getOptimizedStats(?int $workspaceId): array
    {
        // Use optimized raw SQL query to avoid N+1 problems
        $sql = "
            SELECT
                status,
                COUNT(*) as count,
                SUM(CASE WHEN DATEDIFF(end_date, start_date) > 0 THEN DATEDIFF(end_date, start_date) ELSE 0 END) as total_days,
                AVG(CASE WHEN DATEDIFF(end_date, start_date) > 0 THEN DATEDIFF(end_date, start_date) ELSE 0 END) as avg_days
            FROM projects
            " . ($workspaceId ? "WHERE workspace_id = ?" : "") . "
            GROUP BY status
        ";

        $bindings = $workspaceId ? [$workspaceId] : [];
        $stats = \DB::connection('core')->select($sql, $bindings);

        return collect($stats)->map(function($stat) {
            return (object) [
                'status' => $stat->status,
                'count' => (int) $stat->count,
                'total_days' => (int) ($stat->total_days ?? 0),
                'avg_days' => $stat->avg_days ? (float) $stat->avg_days : 0.0
            ];
        })->toArray();
    }
}
