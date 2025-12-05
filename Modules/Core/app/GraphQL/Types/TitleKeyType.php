<?php
namespace Modules\Core\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\InputType;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Symfony\Component\Console\Input\Input;

class TitleKeyType extends GraphQLType
{
    protected $attributes = [
        'name' => 'titleKeyType',
        'description' => 'An input type for title and key',
    ];

    public function fields(): array
    {
        return [
            'title' => [
                'type' => Type::string(),
                'description' => 'The title',
            ],
            'key' => [
                'type' => Type::string(),
                'description' => 'The key',
            ],
        ];
    }
}
