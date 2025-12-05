<?php

namespace Modules\Core\GraphQL\Types\WorkspaceUser;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class WorkspaceUserType  extends GraphQLType
{
    protected $attributes = [
        'name' => 'WorkspaceUser'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int()
            ],
            'name' => [
                'type' => GraphQL::type('Translatable'),
                'resolve'     => function ($root) {
                    return $root->name ? $root->getTranslations('name') : null;
                }
            ],
            'access_grants' => [
                'type' => Type::listOf(GraphQL::type('AccessGrant')),
                'alias' => 'accessGrants',
            ]
        ];
    }
}

