<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Types\User;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
            ],
            'username' => [
                'type' => Type::string(),
            ],
            'email' => [
                'type' => Type::string(),
            ],
            'mobile' => [
                'type' => Type::string(),
            ],
            'other_mobile' => [
                'type' => Type::string(),
            ],
            'national_id' => [
                'type' => Type::string(),
            ],
            'status' => [
                'type' => Type::string(),
            ],
            'name' => [
                'type' => GraphQL::type('Translatable'),
                'resolve' => function ($model) {
                    return $model->name ? $model->getTranslations('name') : null;
                }
            ],
            'is_admin' => [
                'type' => Type::int(),
            ],
            'photo_path' => [
                'type' => Type::string()
            ],
            'default_workspace_id' => [
                'type' => Type::int(),
            ],
            'default_workspace' => [
                'type' => GraphQL::type('Workspace'),
                'alias' => 'defaultWorkspace',
            ]
        ];
    }
}
