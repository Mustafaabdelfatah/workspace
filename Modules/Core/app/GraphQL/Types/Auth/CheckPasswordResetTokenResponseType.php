<?php
namespace Modules\Core\GraphQL\Types\Auth;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CheckPasswordResetTokenResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CheckPasswordResetTokenResponse',
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::boolean(),
            ],
            'message' => [
                'type' => Type::string()
            ],
            'email'=> [
                'type' => Type::string()
            ]
        ];
    }
}
