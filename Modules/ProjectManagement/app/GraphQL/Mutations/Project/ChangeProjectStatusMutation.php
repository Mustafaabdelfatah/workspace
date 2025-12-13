<?php
namespace Modules\ProjectManagement\App\GraphQL\Mutations\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Services\ProjectService;
use Modules\ProjectManagement\App\Traits\GraphQL\GraphQLResponseTrait;
use Modules\ProjectManagement\App\Traits\GraphQL\GraphQLValidationTrait;

class ChangeProjectStatusMutation extends Mutation
{
    use GraphQLResponseTrait, GraphQLValidationTrait;

    protected $attributes = [
        'name' => 'changeProjectStatus',
    ];

    public function __construct(
        private ProjectService $projectService
    ) {}

    public function type(): Type
    {
        return GraphQL::type('ChangeProjectStatusResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function rules(array $args = []): array
    {
        return array_merge(
            $this->getProjectIdValidationRules(),
            $this->getProjectStatusValidationRules()
        );
    }

    public function resolve($root, $args): array
    {
        // Validate input using the shared validation rules
        $validationResult = $this->validateWithRules($args, $this->rules($args));
        if ($validationResult !== null) {
            return $validationResult;
        }

        try {
            $project = $this->projectService->changeProjectStatus(
                projectId: $args['id'],
                status: $args['status']
            );

            return $this->successResponse('Project status changed successfully', $project);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
