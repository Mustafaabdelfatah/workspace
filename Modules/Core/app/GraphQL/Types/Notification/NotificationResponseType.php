<?php
namespace Modules\Core\GraphQL\Types\Notification;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class NotificationResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'NotificationResponse',
        'description' => 'Response containing notifications and pagination',
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
                'type' => Type::listOf(GraphQL::type('Notification')),
                'description' => 'List of Notification records',
            ],
        ];
    }
}
