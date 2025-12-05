<?php
namespace Modules\Core\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class FileDataType extends GraphQLType
{
    protected $attributes = [
        'name' => 'FileData',
        'description' => 'Details of the  file',
    ];

    public function fields(): array
    {
        return [
            'file_type' => [
                'type' => Type::string(),
                'description' => 'The type of the file',
            ],
            'content' => [
                'type' => Type::string(),
                'description' => 'Base64 encoded content of the file',
            ],
            'file_name' => [
                'type' => Type::string(),
                'description' => 'The name of the file',
            ],
            'mime_type' => [
                'type' => Type::string(),
                'description' => 'The MIME type of the file',
            ],
        ];
    }
}
