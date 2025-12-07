<?php
namespace Modules\ProjectManagement\App\GraphQL\Mutations\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Services\ProjectService;

class DeleteProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteProject',
    ];

    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

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

    public function resolve($root, $args)
    {
        $projectId = $args['id'];

        try {
            $result = $this->projectService->deleteProject($projectId);

            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'Project deleted successfully'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to delete project'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to delete project: ' . $e->getMessage()
            ];
        }
    }
}
