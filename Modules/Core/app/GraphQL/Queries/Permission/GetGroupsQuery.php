<?php
namespace Modules\Core\app\GraphQL\Queries\Permission;

use Modules\Core\Models\Group;
use Modules\Core\Repositories\PermissionRepository;
use Rebing\GraphQL\Support\Query;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class GetGroupsQuery extends Query
{
    protected $attributes = [
        'name' => 'getGroups',
    ];

    private $repo;
    public function __construct(PermissionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        return GraphQL::type('GroupsResponse');
    }

    public function args(): array
    {
        return [
            'page' => [
                'type' => Type::int(),
                'description' => 'The page number',
                'defaultValue' => 1,
            ],
            'per_page' => [
                'type' => Type::int(),
                'description' => 'The number of items per page',
                'defaultValue' => 10,
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->getGroups($args);
    }
}
