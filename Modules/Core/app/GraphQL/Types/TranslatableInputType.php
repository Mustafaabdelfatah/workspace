<?php
namespace Modules\Core\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Symfony\Component\Console\Input\Input;

class TranslatableInputType extends InputType
{
    protected $attributes = [
        'name' => 'TranslatableInput',
        'description' => 'An input type for translatable fields',
    ];

    public function fields(): array
    {
        return [
            'en' => [
                'type' => Type::string(),
                'description' => 'The English translation',
            ],
            'ar' => [
                'type' => Type::string(),
                'description' => 'The Arabic translation',
            ],
            // Add more languages as needed
        ];
    }
}
