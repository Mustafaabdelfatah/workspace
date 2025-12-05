<?php
namespace Modules\Core\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AttachmentByUrlType extends GraphQLType
{
    protected $attributes = [
        'name' => 'attachmentByUrlType',
        'description' => 'Details of the  file',
    ];

    public function fields(): array
    {
        return [
            'attachment_name' => [
                'type' => Type::string(),
                'description' => 'The type of the file',
            ],
            'attachment_url' => [
                'type' => Type::string(),
                'description' => 'The type of the file',
            ],
        ];
    }
}
