<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Types\FileVisibility;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class FileVisibilityResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'fileVisibilityResponseType',
        'description' => 'this type defines file visibility response type'
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
                'type' => Type::listOf(GraphQL::type('fileVisibilityType')),
            ],
            'paging' => [
                'type' => GraphQL::type('Paging'),
            ],
        ];
    }
}
