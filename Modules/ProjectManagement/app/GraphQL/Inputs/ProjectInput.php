<?php
namespace Modules\ProjectManagement\App\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

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
                'type' => Type::nonNull(Type::string()),
            ],
            'description' => [
                'type' => Type::string(),
            ],
            'status' => [
                'type' => Type::string(),
            ],
            'manager_id' => [
                'type' => Type::int(),
            ],
            'start_date' => [
                'type' => Type::string(),
            ],
            'end_date' => [
                'type' => Type::string(),
            ],
        ];
    }
}
