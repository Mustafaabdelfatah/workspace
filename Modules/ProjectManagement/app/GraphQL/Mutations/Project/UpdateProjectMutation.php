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
        return GraphQL::type('Project');
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
            throw new \Exception('Validation failed: ' . $validator->errors()->first());
        }

        try {
            return $this->projectService->updateProject($projectId, $input);
        } catch (\Exception $e) {
            throw new \Exception('Failed to update project: ' . $e->getMessage());
        }
    }
}
