<?php
namespace Modules\ProjectManagement\App\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class ProjectFilterInput extends InputType
{
    protected $attributes = [
        'name' => 'ProjectFilterInput',
    ];

    public function fields(): array
    {
        return [
            'workspace_id' => [
                'type' => Type::int(),
                'description' => 'Filter by workspace ID'
            ],
            'status' => [
                'type' => Type::string(),
                'description' => 'Filter by project status'
            ],
            'project_type' => [
                'type' => Type::string(),
                'description' => 'Filter by project type'
            ],
            'entity_type' => [
                'type' => Type::string(),
                'description' => 'Filter by entity type (contractor/consultant/developer)'
            ],
            'search' => [
                'type' => Type::string(),
                'description' => 'Search in project name or code'
            ],
            'owner_id' => [
                'type' => Type::int(),
                'description' => 'Filter by owner ID'
            ],
            'owner_only' => [
                'type' => Type::boolean(),
                'description' => 'Show only projects owned by current user'
            ],
            'manager_id' => [
                'type' => Type::int(),
                'description' => 'Filter by manager ID'
            ],
            'custom_project_type' => [
                'type' => Type::string(),
                'description' => 'Filter by custom project type'
            ],
            'start_date_from' => [
                'type' => Type::string(),
                'description' => 'Filter projects starting from this date'
            ],
            'start_date_to' => [
                'type' => Type::string(),
                'description' => 'Filter projects starting until this date'
            ],
            'end_date_from' => [
                'type' => Type::string(),
                'description' => 'Filter projects ending from this date'
            ],
            'end_date_to' => [
                'type' => Type::string(),
                'description' => 'Filter projects ending until this date'
            ],
            'workspace_details_completed' => [
                'type' => Type::boolean(),
                'description' => 'Filter by workspace details completion status'
            ],
        ];
    }
}
