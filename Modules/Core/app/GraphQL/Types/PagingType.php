<?php
namespace Modules\Core\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PagingType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Paging',
        'description' => 'Pagination details',
    ];

    public function fields(): array
    {
        return [
            'total' => [
                'type' => Type::int(),
                'description' => 'The total number of items',
            ],
            'current_page' => [
                'type' => Type::int(),
                'description' => 'The current page number',
            ],
            'last_page' => [
                'type' => Type::int(),
                'description' => 'The last page number',
            ],
            'from' => [
                'type' => Type::int(),
                'description' => 'The starting item number on the current page',
            ],
            'to' => [
                'type' => Type::int(),
                'description' => 'The ending item number on the current page',
            ],
        ];
    }
}
