<?php
namespace Modules\Core\GraphQL\Mutations\Workspace ;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\WorkspaceRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class SaveWorkspaceMutation  extends Mutation
{
    protected $attributes = [
        'name' => 'saveWorkspace'
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
            'id' => [
                'type' => Type::int(),
            ],
            'name' => [
              'type' => GraphQL::type('TranslatableInput'),
            ],
            'workspace_type' => [
                'type' => Type::string(),
            ],
            'module_id' => [
                'type' => Type::int(),
            ],
        ];
    }

    public function resolve($root, $args)
    {

        $validator = Validator::make($args, [
            'id' => ['nullable', 'exists:workspaces,id'],
            //'module_id' => ['required', 'exists:modules,id'],
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string'],
            'name.ar' => ['required', 'string'],
            'workspace_type' => ['required'],
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        return $this->repo->saveWorkspace($args);
    }
}

