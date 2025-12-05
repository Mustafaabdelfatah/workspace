<?php
namespace Modules\Core\GraphQL\Mutations\UserGroup;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\UserGroupRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class SendUserGroupInvitationMutation extends Mutation
{
    protected $attributes = [
        'name' => 'sendUserGroupInvitation'
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
            'user_group_id' => [
                'type' => Type::nonNull(Type::int())
            ],
            'emails' => [
                'type' => Type::listOf(Type::string())
            ]
        ];
    }

    public function resolve($root, $args)
    {

        $validator = Validator::make($args, [
        'user_group_id' => ['required', 'exists:' . $this->core_connection . '.user_groups,id'],
        'emails' => ['required', 'array'],
        'emails.*' => ['required', 'email'],
    ]);

    if ($validator->fails()) {
        return [
            'status' => false,
            'message' => $validator->errors()->first(),
        ];
    }

        return $this->repo->sendUserGroupInvitation($args);
    }
}

