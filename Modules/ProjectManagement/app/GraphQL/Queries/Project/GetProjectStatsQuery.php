<?php
namespace Modules\ProjectManagement\App\GraphQL\Queries\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Models\Project;

class GetProjectStatsQuery extends Query
{
    protected $attributes = [
        'name' => 'projectStats',
    ];

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

    public function resolve($root, $args)
    {
        $query = Project::query();

        if (isset($args['workspace_id'])) {
            $query->where('workspace_id', $args['workspace_id']);
        }
        $sql = "
            SELECT
                status,
                COUNT(*) as count,
                SUM(CASE WHEN DATEDIFF(end_date, start_date) > 0 THEN DATEDIFF(end_date, start_date) ELSE 0 END) as total_days,
                AVG(CASE WHEN DATEDIFF(end_date, start_date) > 0 THEN DATEDIFF(end_date, start_date) ELSE 0 END) as avg_days
            FROM projects
            " . (isset($args['workspace_id']) ? "WHERE workspace_id = ?" : "") . "
            GROUP BY status
        ";

        $bindings = isset($args['workspace_id']) ? [$args['workspace_id']] : [];
        $stats = \DB::connection('core')->select($sql, $bindings);

        // Convert to objects with proper types
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
