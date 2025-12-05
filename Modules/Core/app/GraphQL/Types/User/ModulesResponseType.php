<?php
namespace Modules\Core\GraphQL\Types\User;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ModulesResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ModulesResponse',
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::boolean()
            ],
            'message' => [
                'type' => Type::string()
            ],
            'modules' => [
                'type' => Type::listOf(Type::string())
            ],
        ];
    }
}
