<?php
namespace Modules\Core\GraphQL\Mutations\UserGroup;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\UserGroupRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class AcceptUserGroupInvitationMutation extends Mutation
{
    protected $attributes = [
        'name' => 'acceptUserGroupInvitation'
    ];
    protected $core_connection;
    private  $repo;

    public function __construct(UserGroupRepository $repo)
    {
        $this->core_connection = config('core.database_connection');
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('GeneralResponse');
    }

    public function args(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string())
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->acceptUserGroupInvitation($args);
    }
}

