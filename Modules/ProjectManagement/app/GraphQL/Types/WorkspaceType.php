<?php

namespace Modules\ProjectManagement\App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class WorkspaceType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ProjectWorkspace'
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
                    if ($root->name) {
                        if (is_array($root->name)) {
                            return $root->name;
                        }

                        if (is_string($root->name)) {
                            $decoded = json_decode($root->name, true);
                            return $decoded ?: null;
                        }
                    }

                    try {
                        $translations = $root->getTranslations('name');
                        return !empty($translations) ? $translations : null;
                    } catch (\Exception $e) {
                        return null;
                    }
                }
            ],
            'logo_path' => [
                'type' => Type::string(),
                'description' => 'Path to workspace logo'
            ],
            'a4_official_path' => [
                'type' => Type::string(),
                'description' => 'Path to official A4 template'
            ],
            'stamp_path' => [
                'type' => Type::string(),
                'description' => 'Path to official stamp'
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
                'resolve' => function ($root) {
                    return $root->owner?->full_name;
                }
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
