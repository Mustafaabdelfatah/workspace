<?php

namespace Modules\ProjectManagement\App\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class CreateProjectInput extends InputType
{
    protected $attributes = [
        'name' => 'CreateProjectInput',
        'description' => 'Input for creating a project'
    ];

    public function fields(): array
    {
        return [
            'project_id' => [
                'type' => Type::id(),
                'description' => 'Project ID for updates (optional - if not provided, creates new project)'
            ],
            'workspace_id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'Workspace ID'
            ],
            'name' => [
                'type' => Type::nonNull(\GraphQL::type('TranslatableInput')),
                'description' => 'Project name (multilingual)'
            ],
            'entity_type' => [
                'type' => Type::string(),
                'description' => 'Entity type (contractor/consultant/developer)'
            ],
            'project_type' => [
                'type' => Type::string(),
                'description' => 'Project type'
            ],
            'custom_project_type' => [
                'type' => Type::string(),
                'description' => 'Custom project type for "other"'
            ],
            'start_date' => [
                'type' => Type::string(),
                'description' => 'Project start date'
            ],
            'end_date' => [
                'type' => Type::string(),
                'description' => 'Project end date'
            ],
            'invitations' => [
                'type' => Type::listOf(\GraphQL::type('InvitationInput')),
                'description' => 'Project invitations'
            ]
        ];
    }
}
