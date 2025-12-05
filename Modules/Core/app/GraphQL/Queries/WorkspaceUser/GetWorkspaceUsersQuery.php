<?php
namespace Modules\Core\GraphQL\Queries\WorkspaceUser;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\WorkspaceUserRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class GetWorkspaceUsersQuery extends Query
{
    protected $attributes = [
        'name' => 'getWorkspaceUsers',
    ];

    private WorkspaceUserRepository $repo;

    public function __construct(WorkspaceUserRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('WorkspaceUsersResponse');
    }

    public function args(): array
    {
        return [
            'workspace_id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'page' => [
                'type' => Type::int(),
            ],
            'per_page' => [
                'type' => Type::int(),
            ],
            'search_key' => [
                'type' => Type::string(),
             ]
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->getWorkspaceUsers($args);

    }
}

