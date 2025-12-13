<?php
namespace Modules\ProjectManagement\App\GraphQL\Mutations\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Http\Requests\CreateProjectRequest;
use Modules\ProjectManagement\App\Services\ProjectService;
use Modules\ProjectManagement\App\Traits\GraphQL\GraphQLResponseTrait;
use Modules\ProjectManagement\App\Traits\GraphQL\GraphQLValidationTrait;

class CreateProjectMutation extends Mutation
{
    use GraphQLResponseTrait, GraphQLValidationTrait;

    protected $attributes = [
        'name' => 'createProject',
    ];

    public function __construct(
        private ProjectService $projectService
    ) {}

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

    public function resolve($root, $args): array
    {
        $input = $args['input'];

        $validationResult = $this->validateInput($input, CreateProjectRequest::class);
        if ($validationResult !== null) {
            return $validationResult;
        }

        try {
            $project = $this->projectService->createProject($input);

            return $this->successResponse('Project created successfully', $project);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create project: ' . $e->getMessage());
        }
    }
}
