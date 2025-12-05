<?php

namespace Modules\Core\GraphQL\Types\WorkspaceUser;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class AccessGrantType  extends GraphQLType
{
    protected $attributes = [
        'name' => 'AccessGrant'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int()
            ],
            'user' => [
                'type' => GraphQL::type('User')
            ],
            'user_group' => [
                'type' => GraphQL::type('UserGroup'),
                'alias' => 'userGroup',
            ],
            'scope_type' => [
                'type' => Type::string()
            ],
            'scope_id' => [
                'type' => Type::int()
            ],
            'role' => [
                'type' => GraphQL::type('Group')
            ]
        ];
    }
}

