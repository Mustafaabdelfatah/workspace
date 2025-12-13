<?php
namespace Modules\ProjectManagement\App\GraphQL\Queries\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Services\ProjectService;
use Modules\ProjectManagement\App\Traits\GraphQL\GraphQLResponseTrait;
use Modules\ProjectManagement\App\Traits\GraphQL\GraphQLValidationTrait;
use Modules\ProjectManagement\App\Traits\Services\ProjectPermissionTrait;
use Illuminate\Support\Facades\Auth;

class GetProjectByIdQuery extends Query
{
    use GraphQLResponseTrait, GraphQLValidationTrait, ProjectPermissionTrait;

    protected $attributes = [
        'name' => 'project',
    ];

    public function __construct(
        private ProjectService $projectService
    ) {}

    public function type(): Type
    {
        return GraphQL::type('ProjectSingleResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
            'detailed' => [
                'name' => 'detailed',
                'type' => Type::boolean(),
                'defaultValue' => false,
            ],
        ];
    }

    public function resolve($root, array $args): array
    {
        $projectId = $args['id'];

        // Validate project ID
        $idValidation = $this->validateWithRules(['id' => $projectId], $this->getProjectIdValidationRules());
        if ($idValidation !== null) {
            return $idValidation;
        }

        try {
            $project = $this->projectService->getProjectById(
                id: $projectId,
                detailed: $args['detailed']
            );

            if (!$project) {
                return $this->errorResponse('Project not found');
            }

            // Use the permission trait method
            if (!$this->hasProjectAccess($project, Auth::user())) {
                return $this->errorResponse('No permission to access this project');
            }

            return $this->successResponse('Project retrieved successfully', $project);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
