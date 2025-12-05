<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Types\User;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class StampUserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'stampUserType',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'title' => [
                'type' => Type::string(),
            ],
            'user' => [
                'type' => GraphQL::type('User'),
                'resolve' => function ($model) {
                    return $model->user;
                },
            ],
            'viewed_at' => [
                'type' => Type::string(),
            ],
            'completed_at' => [
                'type' => Type::string(),
            ],
        ];
    }
}
