<?php
namespace Modules\Core\GraphQL\Queries\Workspace;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\WorkspaceRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class GetWorkspacesQuery extends Query
{
    protected $attributes = [
        'name' => 'getWorkspaces',
    ];

    private WorkspaceRepository $repo;

    public function __construct(WorkspaceRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('WorkspacesResponse');
    }

    public function args(): array
    {
        return [
            'page' => [
                'type' => Type::int(),
            ],
            'per_page' => [
                'type' => Type::int(),
            ],
            'search_key' => [
                'type' => Type::string(),
             ],
             'module_id' => [
                'type' => Type::int(),
             ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->getWorkspaces($args);

    }
}

