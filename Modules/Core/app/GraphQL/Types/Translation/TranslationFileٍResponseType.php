<?php
namespace Modules\Core\GraphQL\Types\Translation;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TranslationFileٍResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TranslationFileٍResponse',
        'description' => 'A file containing translations in Base64 format'
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Status of the request',
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Message related to the request status',
            ],
            'data' => [
                'type' => \GraphQL::type('FileData'),
                'description' => 'File details in Base64 format',
            ],
        ];
    }
}
