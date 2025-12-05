<?php
namespace Modules\Core\GraphQL\Mutations\WorkspaceUser;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\WorkspaceUserRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class RevokeAccessGrantMutation extends Mutation
{
    protected $attributes = [
        'name' => 'revokeAccessGrant'
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
            'workspace_id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'access_grant_ids' => [
                'type' => Type::nonNull(Type::listOf(Type::int())),
            ],
        ];
    }

    public function resolve($root, $args)
    {

        $validator = Validator::make($args, [
            'workspace_id' => ['required', 'exists:' . $this->core_connection . '.workspaces,id'],
            'access_grant_ids' => ['required', 'array'],
            'access_grant_ids.*' => ['required', 'exists:' . $this->core_connection . '.access_grants,id'],
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        return $this->repo->revokeAccessGrant($args);
    }
}

