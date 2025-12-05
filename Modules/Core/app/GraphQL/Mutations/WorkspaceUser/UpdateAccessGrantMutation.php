<?php
namespace Modules\Core\GraphQL\Mutations\WorkspaceUser;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\WorkspaceUserRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class UpdateAccessGrantMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateAccessGrant'
    ];
    protected $core_connection;
    private  $repo;

    public function __construct(WorkspaceUserRepository $repo)
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
            'access_grant_id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'role_id' => [
                'type' => Type::nonNull(Type::int()),
            ],
        ];
    }

    public function resolve($root, $args)
    {

        $validator = Validator::make($args, [
            'access_grant_id' => ['required', 'exists:' . $this->core_connection . '.access_grants,id'],
            'role_id' => ['required', 'exists:' . $this->core_connection . '.groups,id'],
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        return $this->repo->updateAccessGrant($args);
    }
}

