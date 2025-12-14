<?php

namespace Modules\ProjectManagement\App\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class InvitationInput extends InputType
{
    protected $attributes = [
        'name' => 'InvitationInput',
        'description' => 'Input for project invitation'
    ];

    public function fields(): array
    {
        return [
            'user_group_id' => [
                'type' => Type::id(),
                'description' => 'User group ID (team) to invite'
            ],
            'role_id' => [
                'type' => Type::id(),
                'description' => 'Role ID from groups table (for email invitations)'
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'Single email to invite directly'
            ],
            'emails' => [
                'type' => Type::listOf(Type::string()),
                'description' => 'Array of emails for team invitations'
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Invitation message'
            ]
        ];
    }
}
