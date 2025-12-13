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

    public function resolve($root, array $args): array
    {
        $filters = $this->prepareFilters($args);

        try {
            $projects = $this->projectService->getFilteredProjects(
                filters: $filters,
                perPage: $args['per_page'],
                page: $args['page']
            );

            return $this->successListResponse('Projects retrieved successfully', $projects);
        } catch (\Exception $e) {
            return $this->errorListResponse($e->getMessage());
        }
    }

    private function prepareFilters(array $args): array
    {
        $filters = $args['filter'] ?? [];

        // Add ordering to filters
        $filters['order_by'] = $args['order_by'];
        $filters['order_dir'] = $args['order_dir'];

        return $filters;
    }
}
