<?php
namespace Modules\ProjectManagement\App\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class ProjectUpdateInput extends InputType
{
    protected $attributes = [
        'name' => 'ProjectUpdateInput',
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'type' => Type::string(),
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
