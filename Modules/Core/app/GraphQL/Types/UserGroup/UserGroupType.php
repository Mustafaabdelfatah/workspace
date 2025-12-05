<?php

namespace Modules\Core\GraphQL\Types\UserGroup;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UserGroupType extends GraphQLType
{
    protected $attributes = [
        'name' => 'UserGroup'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int()
            ],
            'name' => [
                'type' => GraphQL::type('Translatable'),
                'resolve' => function ($root) {
                    return $root->name ? $root->getTranslations('name') : null;
                }
            ],
            'description' => [
                'type' => GraphQL::type('Translatable'),
                'resolve' => function ($root) {
                    return $root->description ? $root->getTranslations('description') : null; 
                }
            ],
            'workspace_id' => [
                'type' => Type::int(),
            ],
            'workspace' => [
                'type' => GraphQL::type('Workspace'),
                'resolve' => function ($root) {
                    return $root->workspace;
                }
            ],
            'users' => [
                'type' => Type::listOf(GraphQL::type('User'))
            ],
            'writer_name' => [
                'type' => Type::string(),
                'resolve' => function ($root) {
                    return $root->writer?->full_name;
                }
            ],
            'editor_name' => [
                'type' => Type::string(),
                'resolve' => function ($root) {
                    return $root->editor?->full_name;
                }
            ],
            'created_at' => [
                'type' => Type::string(),
            ],
            'updated_at' => [
                'type' => Type::string(),
            ],
        ];
    }
}

