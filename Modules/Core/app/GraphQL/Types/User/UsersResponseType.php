<?php
namespace Modules\Core\GraphQL\Types\User;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UsersResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'UsersResponse',
        'description' => 'The response type for user pagination query',
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Indicates success or failure of the operation',
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Message about the operation',
            ],
            'paging' => [
                'type' => GraphQL::type('Paging'),
                'description' => 'Pagination information',
            ],
            'records' => [
                'type' => Type::listOf(GraphQL::type('User')),
                'description' => 'List of user records',
            ],
        ];
    }
}
