<?php
namespace Modules\ProjectManagement\App\GraphQL\Queries\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Services\ProjectService;
use Illuminate\Support\Facades\Auth;

class GetProjectByIdQuery extends Query
{
    protected $attributes = [
        'name' => 'project',
    ];

    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

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
        ];
    }

    public function resolve($root, $args)
    {
        try {
            $project = $this->projectService->getProjectById($args['id']);

            if (!$project) {
                throw new \Exception('Project not found');
            }

            $user = Auth::user();

            if (!$user->is_admin &&
                $project->owner_id !== $user->id &&
                $project->manager_id !== $user->id &&
                !$project->hasMember($user)) {
                throw new \Exception('Access denied');
            }

            // Load relationships
            $project->load(['workspace', 'owner', 'manager', 'parentProject', 'subProjects', 'tasks', 'members']);

            return [
                'status' => true,
                'message' => 'lang_data_found',
                'record' => $project
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'record' => null
            ];
        }
    }
}