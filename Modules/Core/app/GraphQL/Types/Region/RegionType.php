<?php

namespace Modules\Core\GraphQL\Types\Region;

use GraphQL\Type\Definition\Type;
use Modules\Core\Models\Region;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class RegionType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Region',
        'model' => Region::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
            ],
            'name' => [
                'type' => GraphQL::type('Translatable'),
                'resolve'     => function ($model) {
                    return $model->name ? $model->getTranslations('name') : null;
                }
            ],
              'country'=> [
                'type' => Type::string(),
            ],
            'created_at' => [
                'type' => Type::string(),
            ],
            'updated_at' => [
                'type' => Type::string(),
            ],
            'writer_name' => [
                'type' => Type::string(),
                'resolve'     => function ($model) {
                    return $model->writer ? $model->writer->full_name : null;
                }
            ],
            'editor_name' => [
                'type' => Type::string(),
                'resolve'     => function ($model) {
                    return $model->editor ? $model->editor->full_name : null;
                }
            ],
        ];
    }
}
