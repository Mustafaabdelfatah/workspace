<?php
namespace Modules\ProjectManagement\App\GraphQL\Queries\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Services\ProjectService;

class GetProjectsQuery extends Query
{
    protected $attributes = [
        'name' => 'projects',
    ];

    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function type(): Type
    {
        return GraphQL::type('ProjectsResponse');
    }

    public function args(): array
    {
        return [
            'filter' => [
                'name' => 'filter',
                'type' => GraphQL::type('ProjectFilterInput'),
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'defaultValue' => 1,
            ],
            'per_page' => [
                'name' => 'per_page',
                'type' => Type::int(),
                'defaultValue' => 20,
            ],
            'order_by' => [
                'name' => 'order_by',
                'type' => Type::string(),
                'defaultValue' => 'created_at',
            ],
            'order_dir' => [
                'name' => 'order_dir',
                'type' => Type::string(),
                'defaultValue' => 'DESC',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        // Merge filters and pagination arguments
        $filters = array_merge($args['filter'] ?? [], [
            'order_by' => $args['order_by'] ?? 'created_at',
            'order_dir' => $args['order_dir'] ?? 'DESC'
        ]);

        $perPage = $args['per_page'] ?? 20;
        $page = $args['page'] ?? 1;

        // Set request data for pipeline filters
        request()->merge($filters);

        try {
            $paginator = $this->projectService->getFilteredProjects($filters, $perPage, $page);

            return [
                'status' => true,
                'message' => 'lang_data_found',
                'records' => $paginator->items(),
                'paging' => [
                    'total' => $paginator->total(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Error fetching projects: ' . $e->getMessage(),
                'records' => [],
                'paging' => [
                    'total' => 0,
                    'current_page' => 1,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0,
                ]
            ];
        }
    }
}
