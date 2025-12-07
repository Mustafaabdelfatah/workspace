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
        return Type::boolean();
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
            return $this->projectService->deleteProject($projectId);
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete project: ' . $e->getMessage());
        }
    }
}
