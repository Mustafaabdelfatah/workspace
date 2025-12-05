<?php

declare(strict_types=1);

namespace Modules\Core\GraphQL\Types\User;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class SearchUsers extends GraphQLType
{
    protected $attributes = [
        'name' => 'SearchUsers',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            "status" => [
                "type" => Type::boolean()
            ],
            "message" => [
                "type" => Type::string()
            ],
            "records" => [
                'type' => Type::listOf(GraphQL::type('HRGroup'))
            ]
        ];
    }
}
