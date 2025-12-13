<?php

namespace Modules\ProjectManagement\App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProjectType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Project',
        'description' => 'A project'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the project'
            ],
            'code' => [
                'type' => Type::string(),
                'description' => 'Project code'
            ],
            'name' => [
                'type' => \GraphQL::type('Translatable'),
                'description' => 'Project name (multilingual)',
                'resolve' => function ($root) {
                    // Handle the name field based on how it's stored
                    if (is_array($root->name)) {
                        return $root->name;
                    } elseif (is_string($root->name)) {
                        return json_decode($root->name, true) ?: null;
                    }
                    return null;
                }
            ],
            'user_type' => [
                'type' => Type::string(),
                'description' => 'User type (contractor/consultant/developer)'
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'Project status',
                'resolve' => function ($root) {
                    if ($root->status instanceof \Modules\ProjectManagement\App\Enums\ProjectStatusEnum) {
                        return $root->status->value;
                    }
                    return $root->status;
                }
            ],
            'project_type' => [
                'type' => Type::string(),
                'description' => 'Project type',
                'resolve' => function ($root) {
                    if ($root->project_type instanceof \Modules\ProjectManagement\App\Enums\ProjectTypeEnum) {
                        return $root->project_type->value;
                    }
                    return $root->project_type;
                }
            ],
            'custom_project_type' => [
                'type' => Type::string(),
                'description' => 'Custom project type'
            ],
            'workspace_details_completed' => [
                'type' => Type::boolean(),
                'description' => 'Whether workspace details are completed'
            ],
            'start_date' => [
                'type' => Type::string(),
                'description' => 'Project start date'
            ],
            'end_date' => [
                'type' => Type::string(),
                'description' => 'Project end date'
            ],
            'latitude' => [
                'type' => Type::float(),
                'description' => 'Project latitude'
            ],
            'longitude' => [
                'type' => Type::float(),
                'description' => 'Project longitude'
            ],
            'area' => [
                'type' => Type::float(),
                'description' => 'Project area'
            ],
            'area_unit' => [
                'type' => Type::string(),
                'description' => 'Area unit'
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'Created at timestamp'
            ],
            'owner' => [
                'type' => \GraphQL::type('User'),
                'description' => 'Project owner'
            ],
            'manager' => [
                'type' => \GraphQL::type('User'),
                'description' => 'Project manager'
            ],
            'workspace' => [
                'type' => \GraphQL::type('Workspace'),
                'description' => 'Project workspace'
            ],
            'invitations' => [
                'type' => Type::listOf(\GraphQL::type('ProjectInvitation')),
                'description' => 'Project invitations'
            ]
        ];
    }
}
