<?php
namespace Modules\Core\GraphQL\Mutations\UserGroup;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\UserGroupRepository;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DeleteUserGroupMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteUserGroup',
    ];

    private  $repo;

    public function __construct(UserGroupRepository $repo)
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
                'type' => Type::nonNull(Type::int())
            ],
        ];
    }

    public function resolve($root, $args)
    {

        return $this->repo->deleteUserGroup($args);

    }
}

