<?php
namespace Modules\Core\GraphQL\Types\Permission;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class GroupsResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'GroupsResponse',
        'description' => 'A type representing the response structure for a group query'
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::Boolean(),
            ],
            'message' => [
                'type' => Type::string(),
            ],
            'paging' => [
                'type' => GraphQL::type('Paging'),
            ],
            'records' => [
                'type' => Type::listOf(GraphQL::type('Group')),
            ]
        ];
    }
}
