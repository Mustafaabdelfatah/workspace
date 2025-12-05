<?php
namespace Modules\Core\GraphQL\Queries\Workspace;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\WorkspaceRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class GetWorkspaceByIdQuery extends Query
{
    protected $attributes = [
        'name' => 'getWorkspaceById',
    ];

    private WorkspaceRepository $repo;

    public function __construct(WorkspaceRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('WorkspaceSingleResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
            ]
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->getWorkspaceById($args);

    }
}

