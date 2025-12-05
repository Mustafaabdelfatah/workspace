<?php
namespace Modules\Core\GraphQL\Queries\UserGroup;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\UserGroupRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class GetUserGroupsQuery extends Query
{
    protected $attributes = [
        'name' => 'getUserGroups',
    ];

    private UserGroupRepository $repo;

    public function __construct(UserGroupRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('UserGroupsResponse');
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
            'workspace_id' => [
                'type' => Type::int(),
            ],
            'search_key' => [
                'type' => Type::string(),
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->getUserGroups($args);

    }
}

