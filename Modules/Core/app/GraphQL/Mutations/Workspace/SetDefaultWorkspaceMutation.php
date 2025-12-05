<?php
namespace Modules\Core\GraphQL\Mutations\Workspace;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\WorkspaceRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class SetDefaultWorkspaceMutation  extends Mutation
{
    protected $attributes = [
        'name' => 'setDefaultWorkspace',
    ];

    private  $repo;

    public function __construct(WorkspaceRepository $repo)
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
            'workspace_id' => [
                'type' => Type::nonNull(Type::int())
            ],
        ];
    }

    public function resolve($root, $args)
    {

        $validator = Validator::make($args, [
            'workspace_id' => ['required', 'exists:workspaces,id'],
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first(),
            ];
        }
        return $this->repo->setDefaultWorkspace($args);

    }
}

