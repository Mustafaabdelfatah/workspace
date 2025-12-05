<?php
namespace Modules\Core\GraphQL\Mutations\Permission;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\PermissionRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateEditGroupMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createEditGroup',
    ];

    private $repo;

    public function __construct(PermissionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        return GraphQL::type('GeneralResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'The ID of the group to edit (optional for creating new group)',
            ],
            'name' => [
                'type' => GraphQL::type('TranslatableInput'),
            ],
            'group_key' => [
                'type' => Type::string(),
                'description' => 'Name of the group key',
            ],
            'module_id' => [   
                'type' => Type::int(),
            ],
            'permissions' => [
                'type' => Type::listOf(Type::string()),
                'description' => 'List of permissions to assign to the group',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->createEditGroup($args);
    }
}
