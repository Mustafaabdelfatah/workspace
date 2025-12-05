<?php
namespace Modules\Core\GraphQL\Types\Translation;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TranslationTemplateDataType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TranslationTemplateData',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'export_type' => [
                'type' => Type::string(),
            ],
            'content' => [
                'type' => Type::string(),
            ],
            'file_name' => [
                'type' => Type::string(),
            ],
            'mime_type' => [
                'type' =>Type::string(),
            ],
        ];
    }
}
