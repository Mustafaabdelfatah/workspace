<?php
namespace Modules\ProjectManagement\App\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ProjectInput extends InputType
{
    protected $attributes = [
        'name' => 'ProjectInput',
    ];

    public function fields(): array
    {
        return [
            'workspace_id' => [
                'type' => Type::nonNull(Type::int()),
            ],
            'name' => [
                'type' => Type::nonNull(GraphQL::type('TranslatableInput')),
            ],
            'description' => [
                'type' => GraphQL::type('TranslatableInput'),
            ],
            'status' => [
                'type' => Type::string(),
            ],
            'project_type' => [
                'type' => Type::string(),
            ],
            'building_type' => [
                'type' => Type::string(),
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
            'start_date' => [
                'type' => Type::string(),
            ],
            'end_date' => [
                'type' => Type::string(),
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
        ];
    }
}
