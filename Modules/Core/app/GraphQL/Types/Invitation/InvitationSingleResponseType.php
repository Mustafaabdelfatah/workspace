<?php

namespace Modules\Core\GraphQL\Types\Invitation;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InvitationSingleResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'InvitationSingleResponse'
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::boolean(),
            ],
            'message' => [
                'type' => Type::string(),
            ],
            'record' => [
                "type" => GraphQL::type("Invitation"),
            ]
        ];
    }
}

