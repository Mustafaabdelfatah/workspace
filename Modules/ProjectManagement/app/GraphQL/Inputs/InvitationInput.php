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
            'user_id' => [
                'type' => Type::id(),
                'description' => 'User ID to invite'
            ],
            'group_id' => [
                'type' => Type::id(),
                'description' => 'User group ID to invite'
            ],
            'role' => [
                'type' => Type::string(),
                'description' => 'Member role'
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Invitation message'
            ]
        ];
    }
}
