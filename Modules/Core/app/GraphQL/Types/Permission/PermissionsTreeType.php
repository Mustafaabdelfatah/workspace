<?php

namespace Modules\Core\GraphQL\Types\Permission;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PermissionsTreeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PermissionsTree',
        'description' => 'A type that represents the permissions tree',
    ];

    public function fields(): array
    {
        return [
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the permission',
            ],
            'value' => [
                'type' => Type::string(),
                'description' => 'The value of the permission',
            ],
            'children' => [
                'type' => Type::listOf(GraphQL::type('PermissionsTree')),
                'description' => 'The child permissions',
            ],
        ];
    }
}
