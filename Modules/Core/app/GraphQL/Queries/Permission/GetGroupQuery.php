<?php
namespace Modules\Core\app\GraphQL\Queries\Permission;

use Modules\Core\Models\Group;
use Modules\Core\Repositories\PermissionRepository;
use Rebing\GraphQL\Support\Query;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class GetGroupQuery extends Query
{
    protected $attributes = [
        'name' => 'getGroup',
    ];
    private $repo;
    public function __construct(PermissionRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('GroupResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'The ID of the group'
            ]
        ];
    }

    public function resolve($root, $args)
    {
            return $this->repo->getGroup($args);
    }
}
