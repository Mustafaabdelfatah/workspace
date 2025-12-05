<?php

declare(strict_types=1);

namespace Modules\Core\GraphQL\Types\Auth;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class LoginResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'LoginResponse',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'message' => [
                'type' => Type::string(),
            ],
            'status' => [
                'type' => Type::boolean(),
            ],
            'need_otp' => [
                'type' => Type::boolean(),
            ],
            'otp_length' => [
                'type' => Type::int(),
            ],
            'otp_expired_seconds' => [
                'type' => Type::int(),
            ],
            'token' => [
                'type' => Type::string(),
            ],
            'expire' => [
                'type' => Type::string(),
            ],
            'data' => [
                'type' => GraphQL::type('User'),
            ],
        ];
    }
}
