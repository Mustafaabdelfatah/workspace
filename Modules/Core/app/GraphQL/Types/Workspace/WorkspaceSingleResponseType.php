<?php

namespace Modules\Core\GraphQL\Types\Workspace;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class WorkspaceSingleResponseType  extends GraphQLType
{
    protected $attributes = [
        'name' => 'WorkspaceSingleResponse'
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
            'data' => [
                "type" => GraphQL::type("Workspace"),
            ]
        ];
    }
}

