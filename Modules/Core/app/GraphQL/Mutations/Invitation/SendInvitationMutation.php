<?php
namespace Modules\Core\GraphQL\Mutations\Invitation;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\InvitationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class SendInvitationMutation extends Mutation
{
    protected $attributes = [
        'name' => 'sendInvitation'
    ];
    protected $core_connection;
    private  $repo;

    public function __construct(InvitationRepository $repo)
    {
        $this->core_connection = config('core.database_connection');
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('InvitationSingleResponse');
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
            'emails' => [
                'type' => Type::listOf(Type::string())
            ],
            'items' => [
                'type' => Type::listOf(GraphQL::type('InvitationItemInput'))
            ]
        ];
    }

    public function resolve($root, $args)
    {

        $validator = Validator::make($args, [
            'id' => ['nullable', 'exists:' . $this->core_connection . '.invitations,id'],
            'workspace_id' => ['required', 'exists:' . $this->core_connection . '.workspaces,id'],
            'emails' => ['required', 'array'],
            'emails.*' => ['required', 'email'],
            'items' => ['required', 'array'],
            'items.*.scope_type' => ['required', 'string'],
            'items.*.scope_id' => ['nullable', 'integer'],
            'items.*.role_id' => ['required', 'exists:' . $this->core_connection . '.groups,id'],
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        return $this->repo->sendInvitation($args);
    }
}

