<?php

namespace Modules\ProjectManagement\App\GraphQL\Mutations;

use Illuminate\Http\Request;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Services\ProjectService;
use Modules\ProjectManagement\App\Http\Requests\CreateProjectRequest;

class CreateProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProject',
        'description' => 'Create or update a project'
    ];

    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function type(): Type
    {
        return GraphQL::type('CreateProjectResponse');
    }

    public function args(): array
    {
        return [
            'input' => [
                'name' => 'input',
                'type' => GraphQL::type('CreateProjectInput'),
            ]
        ];
    }

    public function resolve($root, array $args)
    {
        $input = $args['input'];

        $request = new CreateProjectRequest();
        $request->merge($input);
        $request->setContainer(app());

        $validator = app('validator')->make($input, $request->rules(), $request->messages());

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
                'workspace_incomplete' => null,
                'missing_fields' => null,
                'project' => null
            ];
        }

        return $this->projectService->createProject($request);
    }
}
