<?php

namespace Modules\Core\GraphQL\Types\Invitation;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class InvitationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Invitation'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int()
            ],
            'token' => [
                'type' => Type::string()
            ],
            'workspace_id' => [
                'type' => Type::int()
            ],
            'workspace' => [
                'type' => GraphQL::type('Workspace'),
                'resolve' => function ($root) {
                    return $root->workspace;
                }
            ],
            'email' => [
                'type' => Type::string()
            ],
            'invited_by' => [
                'type' => Type::int()
            ],
            'invited_by_user' => [
                'type' => GraphQL::type('User'),
                'resolve' => function ($root) {
                    return $root->invitedBy;
                }
            ],
            'expires_at' => [
                'type' => Type::string(),
            ],
            'accepted_at' => [
                'type' => Type::string(),
            ],
            'writer_id' => [
                'type' => Type::int()
            ],
            'writer' => [
                'type' => GraphQL::type('User'),
                'resolve' => function ($root) {
                    return $root->writer;
                }
            ],
            'writer_name' => [
                'type' => Type::string(),
                'resolve' => function ($root) {
                    return $root->writer?->full_name;
                }
            ],
            'invitation_items' => [
                'type' => Type::listOf(GraphQL::type('InvitationItem')),
                'resolve' => function ($root) {
                    return $root->items;
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

