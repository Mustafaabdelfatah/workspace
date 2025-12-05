<?php

namespace Modules\Core\GraphQL\Types\Bank;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BanksResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'BanksResponse',
        'description' => 'A type for Banks data',
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
                "type" => Type::listOf(GraphQL::type("Bank")),
            ]
        ];
    }
}
