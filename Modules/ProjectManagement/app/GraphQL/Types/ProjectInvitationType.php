<?php

namespace Modules\ProjectManagement\App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProjectInvitationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ProjectInvitation',
        'description' => 'A project invitation'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The id of the invitation'
            ],
            'token' => [
                'type' => Type::string(),
                'description' => 'Invitation token'
            ],
            'role' => [
                'type' => Type::string(),
                'description' => 'Member role',
                'resolve' => function ($root) {
                    if ($root->role instanceof \Modules\ProjectManagement\App\Enums\ProjectMemberRoleEnum) {
                        return $root->role->value;
                    }
                    return $root->role;
                }
            ],
            'expires_at' => [
                'type' => Type::string(),
                'description' => 'Expiration date'
            ],
            'accepted_at' => [
                'type' => Type::string(),
                'description' => 'Acceptance date'
            ],
            'declined_at' => [
                'type' => Type::string(),
                'description' => 'Decline date'
            ],
            'invitedUser' => [
                'type' => \GraphQL::type('User'),
                'description' => 'Invited user'
            ],
            'userGroup' => [
                'type' => \GraphQL::type('UserGroup'),
                'description' => 'Invited user group'
            ]
        ];
    }
}
