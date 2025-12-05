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
            ],
            'status' => [
                'type' => Type::string(),
            ],
            'search' => [
                'type' => Type::string(),
            ],
            'owner_id' => [
                'type' => Type::int(),
            ],
        ];
    }
}
