<?php
namespace Modules\ProjectManagement\App\GraphQL\Mutations\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Services\ProjectService;
use Modules\ProjectManagement\App\Traits\GraphQL\GraphQLResponseTrait;
use Modules\ProjectManagement\App\Traits\GraphQL\GraphQLValidationTrait;

class DeleteProjectMutation extends Mutation
{
    use GraphQLResponseTrait, GraphQLValidationTrait;

    protected $attributes = [
        'name' => 'deleteProject',
    ];

    public function __construct(
        private ProjectService $projectService
    ) {}

    public function type(): Type
    {
        return GraphQL::type('DeleteProjectResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ]
        ];
    }

    public function resolve($root, $args): array
    {
        $projectId = $args['id'];

        // Validate project ID
        $idValidation = $this->validateWithRules(['id' => $projectId], $this->getProjectIdValidationRules());
        if ($idValidation !== null) {
            return $idValidation;
        }

        try {
            $result = $this->projectService->deleteProject($projectId);

            return $result
                ? $this->simpleSuccessResponse('Project deleted successfully')
                : $this->simpleErrorResponse('Failed to delete project');

        } catch (\Exception $e) {
            return $this->simpleErrorResponse('Failed to delete project: ' . $e->getMessage());
        }
    }
}
