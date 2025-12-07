<?php
namespace Modules\ProjectManagement\App\GraphQL\Mutations\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Http\Requests\CreateProjectRequest;
use Modules\ProjectManagement\App\Services\ProjectService;

class CreateProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProject',
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
                'type' => Type::nonNull(GraphQL::type('ProjectInput')),
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $input = $args['input'];

        // Create and validate request
        $request = new CreateProjectRequest();
        $request->merge($input);

        $validator = \Validator::make($input, $request->rules(), $request->messages(), $request->attributes());

        if ($validator->fails()) {
            return [
                'status' => 'error',
                'message' => 'Validation failed: ' . $validator->errors()->first(),
                'record' => null
            ];
        }

        try {
            $project = $this->projectService->createProject($input);

            return [
                'status' => 'success',
                'message' => 'Project created successfully',
                'record' => $project
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to create project: ' . $e->getMessage(),
                'record' => null
            ];
        }
    }
}
