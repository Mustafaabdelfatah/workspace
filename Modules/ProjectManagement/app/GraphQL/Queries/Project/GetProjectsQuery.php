<?php
namespace Modules\ProjectManagement\App\GraphQL\Queries\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Services\ProjectService;
use Modules\ProjectManagement\App\Traits\GraphQL\GraphQLResponseTrait;

class GetProjectsQuery extends Query
{
    use GraphQLResponseTrait;

    protected $attributes = [
        'name' => 'projects',
    ];

    public function __construct(
        private ProjectService $projectService
    ) {}

    public function type(): Type
    {
        return GraphQL::type('ProjectsResponse');
    }

    public function args(): array
    {
        return [
            'workspaceId' => [
                'name' => 'workspaceId',
                'type' => Type::nonNull(Type::id()),
                'description' => 'Workspace ID to filter projects'
            ],
            'filter' => [
                'name' => 'filter',
                'type' => GraphQL::type('ProjectFilterInput'),
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'defaultValue' => 1,
            ],
            'first' => [
                'name' => 'first',
                'type' => Type::int(),
                'defaultValue' => 20,
                'description' => 'Number of items per page'
            ],
            'order_by' => [
                'name' => 'order_by',
                'type' => Type::string(),
                'defaultValue' => 'created_at',
            ],
            'order_dir' => [
                'name' => 'order_dir',
                'type' => Type::string(),
                'defaultValue' => 'desc',
            ],
        ];
    }

    public function resolve($root, array $args): array
    {
        $workspaceId = $args['workspaceId'];
        $filters = $this->prepareFilters($args);

        // Add workspace ID to filters
        $filters['workspace_id'] = $workspaceId;

        try {
            $paginatedProjects = $this->projectService->getFilteredProjects(
                filters: $filters,
                perPage: $args['first'] ?? 20,
                page: $args['page'] ?? 1
            );

            // Format the response to match ProjectsResponseType structure
            return [
                'data' => $paginatedProjects->items(),
                'paginatorInfo' => [
                    'total' => $paginatedProjects->total(),
                    'count' => $paginatedProjects->count(),
                    'currentPage' => $paginatedProjects->currentPage(),
                    'lastPage' => $paginatedProjects->lastPage(),
                    'hasMorePages' => $paginatedProjects->hasMorePages(),
                    'perPage' => $paginatedProjects->perPage(),
                    'from' => $paginatedProjects->firstItem(),
                    'to' => $paginatedProjects->lastItem(),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'data' => [],
                'paginatorInfo' => [
                    'total' => 0,
                    'count' => 0,
                    'currentPage' => 1,
                    'lastPage' => 1,
                    'hasMorePages' => false,
                    'perPage' => $args['first'] ?? 20,
                    'from' => null,
                    'to' => null,
                ]
            ];
        }
    }

    private function prepareFilters(array $args): array
    {
        $filters = $args['filter'] ?? [];

        // Add ordering to filters
        $filters['order_by'] = $args['order_by'] ?? 'created_at';
        $filters['order_dir'] = $args['order_dir'] ?? 'desc';

        return $filters;
    }
}
