<?php
namespace Modules\Core\GraphQL\Mutations\UserGroup;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\UserGroupRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class SaveUserGroupMutation extends Mutation
{
    protected $attributes = [
        'name' => 'saveUserGroup'
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
            'id' => [
                'type' => Type::int(),
            ],
            'workspace_id' => [
                'type' => Type::nonNull(Type::int())
            ],
            'name' => [
                'type' => GraphQL::type('TranslatableInput'),
            ],
            'description' => [
                'type' => GraphQL::type('TranslatableInput'),
            ],
            'emails' => [
                'type' => Type::listOf(Type::string())
            ]
        ];
    }

    public function resolve($root, $args)
    {

        $validator = Validator::make($args, [
            'id' => ['nullable', 'exists:' . $this->core_connection . '.user_groups,id'],
            'workspace_id' => ['required', 'exists:' . $this->core_connection . '.workspaces,id'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
            'description' => ['nullable', 'array'],
            'description.en' => ['nullable', 'string'],
            'description.ar' => ['nullable', 'string'],
            'emails' => ['nullable', 'array'],
            'emails.*' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        return $this->repo->saveUserGroup($args);
    }
}

