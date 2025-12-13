<?php

namespace Modules\ProjectManagement\App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserTypeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'UserTypeType',
        'description' => 'User type enum values'
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
