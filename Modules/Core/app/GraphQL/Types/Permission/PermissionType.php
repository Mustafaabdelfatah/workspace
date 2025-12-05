<?php
namespace Modules\Core\GraphQL\Types\Permission;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PermissionType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Permission',
        'description' => 'A type representing a permission'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'The ID of the permission'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the permission'
            ],
            'guard_name' => [
                'type' => Type::string(),
                'description' => 'The guard name of the permission'
            ]
        ];
    }
}
