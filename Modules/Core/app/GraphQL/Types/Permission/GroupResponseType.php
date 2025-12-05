<?php
namespace Modules\Core\GraphQL\Types\Permission;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class GroupResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'GroupResponse',
        'description' => 'A type representing the response structure for a group query'
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Status of the request'
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Message of the request'
            ],
            'data' => [
                'type' => GraphQL::type('Group'),
                'description' => 'The group data'
            ]
        ];
    }
}
