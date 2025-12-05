<?php
namespace Modules\Core\GraphQL\Inputs;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class StampUserInput extends InputType
{
    protected $attributes = [
        'name' => 'stampUserInput',
        'description' => 'this input defines stamp user inputs'
    ];

    public function fields(): array
    {
        return [
            'title' => [
                'type' => Type::string(),
            ],
            'user_id' => [
                'type' => Type::int(),
            ],
        ];
    }
}
