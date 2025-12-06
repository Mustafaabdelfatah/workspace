<?php
namespace Modules\ProjectManagement\App\GraphQL\Types\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProjectType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Project',
        'model' => \Modules\ProjectManagement\App\Models\Project::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'code' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'description' => [
                'type' => Type::string(),
            ],
            'status' => [
                'type' => Type::string(),
                'resolve' => function($project) {
                    return $project->status ? $project->status->value : null;
                }
            ],
            'start_date' => [
                'type' => Type::string(),
                'resolve' => function($project) {
                    return $project->start_date?->format('Y-m-d');
                }
            ],
            'end_date' => [
                'type' => Type::string(),
                'resolve' => function($project) {
                    return $project->end_date?->format('Y-m-d');
                }
            ],
            'workspace_id' => [
                'type' => Type::int(),
            ],
            'owner_id' => [
                'type' => Type::int(),
            ],
            'manager_id' => [
                'type' => Type::int(),
            ],
            'workspace' => [
                'type' => GraphQL::type('Workspace'),
            ],
            'owner' => [
                'type' => GraphQL::type('User'),
            ],
            'manager' => [
                'type' => GraphQL::type('User'),
            ],
            'tasks' => [
                'type' => Type::listOf(GraphQL::type('Task')),
                'resolve' => function($project) {
                    return $project->tasks;
                }
            ],
            'members' => [
                'type' => Type::listOf(GraphQL::type('User')),
                'resolve' => function($project) {
                    return $project->members;
                }
            ],
            'tasks_count' => [
                'type' => Type::int(),
                'resolve' => function($project) {
                    return $project->tasks()->count();
                }
            ],
            'members_count' => [
                'type' => Type::int(),
                'resolve' => function($project) {
                    return $project->members()->count();
                }
            ],
            'created_at' => [
                'type' => Type::string(),
                'resolve' => function($project) {
                    return $project->created_at->format('Y-m-d H:i:s');
                }
            ],
            'updated_at' => [
                'type' => Type::string(),
                'resolve' => function($project) {
                    return $project->updated_at->format('Y-m-d H:i:s');
                }
            ],
        ];
    }
}
