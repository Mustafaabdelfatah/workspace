<?php

namespace Modules\Core\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class InvitationItemInput extends InputType
{
    protected $attributes = [
        'name' => 'InvitationItemInput',
    ];

    public function fields(): array
    {
        return [
            'scope_type' => [
                'type' => Type::string(),
            ],
            'scope_id' => [
                'type' => Type::int(),
            ],
            'role_id' => [
                'type' => Type::int(),
            ],
        ];
    }
}

