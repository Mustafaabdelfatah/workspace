<?php
namespace Modules\Core\GraphQL\Types\Translation;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TranslationTemplateResponseType extends GraphQLType
{
     protected $attributes = [
        'name' => 'TranslationTemplateResponse',
        'description' => 'The response type for TranslationTemplateResponse',
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::nonNull(Type::boolean()),
            ],
            'message' => [
                'type' => Type::string(),
            ],
            'data' => [
                'type' =>GraphQL::type('TranslationTemplateData'),
            ],
        ];
    }
}
