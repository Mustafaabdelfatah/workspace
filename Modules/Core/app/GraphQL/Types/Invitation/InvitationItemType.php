<?php

namespace Modules\Core\GraphQL\Types\Invitation;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvitationItemType extends GraphQLType
{
    protected $attributes = [
        'name' => 'InvitationItem'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int()
            ],
            'invitation_id' => [
                'type' => Type::int()
            ],
            'scope_type' => [
                'type' => Type::string()
            ],
            'scope_id' => [
                'type' => Type::int()
            ],
            'scope' => [
                'type' => GraphQL::type('MorphType'),
                'resolve' => function ($root) {
                    return $root->scope;
                }
            ],
            'group_id' => [
                'type' => Type::int()
            ],
            'group' => [
                'type' => GraphQL::type('Group'),
                'resolve' => function ($root) {
                    return $root->group;
                }
            ],
            'created_at' => [
                'type' => Type::string(),
            ],
            'updated_at' => [
                'type' => Type::string(),
            ],
        ];
    }
}

