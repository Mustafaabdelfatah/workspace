<?php

namespace Modules\ProjectManagement\App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PaginatorInfoType extends GraphQLType
{
    protected $attributes = [
        'name' => 'PaginatorInfo',
        'description' => 'Laravel pagination information'
    ];

    public function fields(): array
    {
        return [
            'total' => [
                'type' => Type::int(),
                'description' => 'Total number of items'
            ],
            'count' => [
                'type' => Type::int(),
                'description' => 'Number of items on current page'
            ],
            'currentPage' => [
                'type' => Type::int(),
                'description' => 'Current page number'
            ],
            'lastPage' => [
                'type' => Type::int(),
                'description' => 'Last page number'
            ],
            'hasMorePages' => [
                'type' => Type::boolean(),
                'description' => 'Whether there are more pages'
            ],
            'perPage' => [
                'type' => Type::int(),
                'description' => 'Items per page'
            ],
            'from' => [
                'type' => Type::int(),
                'description' => 'First item number on current page'
            ],
            'to' => [
                'type' => Type::int(),
                'description' => 'Last item number on current page'
            ],
        ];
    }
}
