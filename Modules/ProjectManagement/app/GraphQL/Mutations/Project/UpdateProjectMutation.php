<?php
namespace Modules\ProjectManagement\App\GraphQL\Mutations\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Http\Requests\UpdateProjectRequest;
use Modules\ProjectManagement\App\Services\ProjectService;
use Modules\ProjectManagement\App\Traits\GraphQL\GraphQLResponseTrait;
use Modules\ProjectManagement\App\Traits\GraphQL\GraphQLValidationTrait;

class UpdateProjectMutation extends Mutation
{
    use GraphQLResponseTrait, GraphQLValidationTrait;

    protected $attributes = [
        'name' => 'updateProject',
    ];

    public function __construct(
        private ProjectService $projectService
    ) {}

    public function type(): Type
    {
        return GraphQL::type('UpdateProjectResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
            ],
            'input' => [
                'type' => Type::nonNull(GraphQL::type('ProjectUpdateInput')),
            ]
        ];
    }

    public function resolve($root, $args): array
    {
        $input = $args['input'];
        $projectId = $args['id'];

        // Validate project ID
        $idValidation = $this->validateWithRules(['id' => $projectId], $this->getProjectIdValidationRules());
        if ($idValidation !== null) {
            return $idValidation;
        }

        // Validate input data
        $validationResult = $this->validateInput($input, UpdateProjectRequest::class);
        if ($validationResult !== null) {
            return $validationResult;
        }

        try {
            $project = $this->projectService->updateProject($projectId, $input);

            return $this->successResponse('Project updated successfully', $project);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update project: ' . $e->getMessage());
        }
    }
}
