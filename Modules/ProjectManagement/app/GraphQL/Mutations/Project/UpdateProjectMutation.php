<?php
namespace Modules\ProjectManagement\App\GraphQL\Mutations\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Http\Requests\UpdateProjectRequest;
use Modules\ProjectManagement\App\Services\ProjectService;

class UpdateProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProject',
    ];

    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    public function type(): Type
    {
        return GraphQL::type('UpdateProjectResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'input' => [
                'type' => Type::nonNull(GraphQL::type('ProjectUpdateInput')),
            ]
        ];
    }

    public function resolve($root, $args)
    {
        $input = $args['input'];
        $projectId = $args['id'];

        // Create and validate request
        $request = new UpdateProjectRequest();
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
            $project = $this->projectService->updateProject($projectId, $input);

            return [
                'status' => 'success',
                'message' => 'Project updated successfully',
                'record' => $project
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to update project: ' . $e->getMessage(),
                'record' => null
            ];
        }
    }
}
