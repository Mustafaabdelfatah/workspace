<?php

namespace Modules\ProjectManagement\App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProjectTypeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ProjectTypeType',
        'description' => 'Project type enum values'
    ];

    public function fields(): array
    {
        return [
            'value' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The enum value'
            ],
            'label' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The display label'
            ]
        ];
    }
}
