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
                'type' => Type::nonNull(GraphQL::type('Translatable')),
                'resolve' => function($project) {
                    if (is_array($project->name)) {
                        return $project->name;
                    }
                    // If name is a simple string, return it for both languages
                    return [
                        'en' => $project->name,
                        'ar' => $project->name,
                    ];
                }
            ],
            'code' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'description' => [
                'type' => GraphQL::type('Translatable'),
                'resolve' => function($project) {
                    // If description is stored as JSON with translations
                    if (is_array($project->description)) {
                        return $project->description;
                    }
                    // If description is a simple string, return it for both languages
                    return [
                        'en' => $project->description,
                        'ar' => $project->description,
                    ];
                }
            ],
            'status' => [
                'type' => Type::string(),
                'resolve' => function($project) {
                    return $project->status ? $project->status->value : null;
                }
            ],
            'project_type' => [
                'type' => Type::string(),
                'resolve' => function($project) {
                    return $project->project_type ? $project->project_type->value : null;
                }
            ],
            'building_type' => [
                'type' => Type::string(),
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
            'parent_project_id' => [
                'type' => Type::int(),
            ],
            'company_id' => [
                'type' => Type::int(),
            ],
            'company_position_id' => [
                'type' => Type::int(),
            ],
            'latitude' => [
                'type' => Type::float(),
            ],
            'longitude' => [
                'type' => Type::float(),
            ],
            'area' => [
                'type' => Type::float(),
            ],
            'area_unit' => [
                'type' => Type::string(),
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
            'parent_project' => [
                'type' => GraphQL::type('Project'),
                'resolve' => function($project) {
                    return $project->parentProject;
                }
            ],
            'sub_projects' => [
                'type' => Type::listOf(GraphQL::type('Project')),
                'resolve' => function($project) {
                    return $project->subProjects;
                }
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
            'sub_projects_count' => [
                'type' => Type::int(),
                'resolve' => function($project) {
                    return $project->subProjects()->count();
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
