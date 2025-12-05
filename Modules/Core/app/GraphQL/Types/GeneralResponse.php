<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class GeneralResponse extends GraphQLType
{
    protected $attributes = [
        'name' => 'GeneralResponse',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::boolean()
            ],
            'message' => [
                'type' => Type::string()
            ],
            'transaction_request_id' => [
                'type' => Type::string(),
                'description' => 'The id of '
            ],
            'id' => [
                'type' => Type::int()
            ]
        ];
    }
}
