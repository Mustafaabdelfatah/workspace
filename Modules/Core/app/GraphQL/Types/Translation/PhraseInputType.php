<?php
namespace Modules\Core\GraphQL\Types\Translation;

use Rebing\GraphQL\Support\InputType;
use GraphQL\Type\Definition\Type;

class PhraseInputType extends InputType
{
    protected $attributes = [
        'name' => 'PhraseInput',
        'description' => 'Input type for translations',
    ];

    public function fields(): array
    {
        return [
            'key' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'phrases' => [
                'type' => \GraphQL::type('TranslatableInput'),
            ],
        ];
    }
}
