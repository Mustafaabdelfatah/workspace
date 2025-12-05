<?php

namespace Modules\Core\GraphQL\Types\Workspace;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class WorkspaceType  extends GraphQLType
{
    protected $attributes = [
        'name' => 'Workspace'
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
            'workspace_type' => [
                'type' => Type::string()
            ],
            'is_owner' => [
                'type' => Type::boolean()
            ],
            'owner_id' => [
                'type' => Type::int()
            ],
            'owner_name' => [
                'type' => Type::string(),
                'resolve'     => function ($root) {
                    return $root->owner?->full_name;
                }
            ],
            'writer_name' => [
                'type' => Type::string(),
                'resolve'     => function ($root) {
                    return $root->writer?->full_name;
                }
            ],
            'editor_name' => [
                'type' => Type::string(),
                'resolve'     => function ($root) {
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

