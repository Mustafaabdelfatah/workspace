<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Types\FileVisibility;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class FileVisibilityType extends GraphQLType
{
    protected $attributes = [
        'name' => 'fileVisibilityType',
        'description' => 'this type defines file visibility type'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::string(),
            ],
            'title' => [
                'type' => Type::string(),
            ],
        ];
    }
}
