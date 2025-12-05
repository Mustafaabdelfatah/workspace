<?php

namespace Modules\Core\GraphQL\Types\Invitation;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InvitationsResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'InvitationsResponse'
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
            'paging' => [
                "type" => GraphQL::type("Paging"),
            ],
            'records' => [
                "type" => Type::listOf(GraphQL::type("Invitation")),
            ]
        ];
    }
}

