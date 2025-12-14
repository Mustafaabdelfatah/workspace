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
                'type' => Type::nonNull(Type::id()),
            ],
            'detailed' => [
                'name' => 'detailed',
                'type' => Type::boolean(),
                'defaultValue' => false,
            ],
        ];
    }

    public function resolve($root, array $args)
    {
        $projectId = $args['id'];

        // Validate project ID
        $idValidation = $this->validateWithRules(['id' => $projectId], $this->getProjectIdValidationRules());
        if ($idValidation !== null) {
            throw new \Exception('Invalid project ID');
        }

        try {
            $project = $this->projectService->getProjectById(
                id: $projectId,
                detailed: $args['detailed']
            );

            if (!$project) {
                throw new \Exception('Project not found');
            }

            // Use the permission trait method
            if (!$this->hasProjectAccess($project, Auth::user())) {
                throw new \Exception('No permission to access this project');
            }

            // Return project directly to match the updated ProjectSingleResponseType
            return $project;
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
