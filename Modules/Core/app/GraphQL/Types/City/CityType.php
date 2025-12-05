<?php
namespace Modules\Core\GraphQL\Types\City;

use GraphQL\Type\Definition\Type;
use Modules\Core\Models\City;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CityType extends GraphQLType
{
    protected $attributes = [
        'name' => 'City',
        'description' => 'A City',
        'model' => City::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::id()),
             ],
            'name' => [
                'type' => \GraphQL::type('Translatable'),
                 'resolve'     => function ($model){
                    return $model->name ? $model->getTranslations('name') : null ;
                }
            ],
            'nationality' => [
                'type' => Type::string(),
             ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation date of the project',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The last update date of the project',
            ],
            'writer_name' => [
                'type' => Type::string(),
                'description' => 'Full name of the writer with ID',
                'resolve'     => function ($model){
                    return $model->writer ? $model->writer->full_name : null ;
                }
            ],
            'editor_name' => [
                'type' => Type::string(),
                'description' => 'Full name of the editor with ID',
                'resolve'     => function ($model){
                    return $model->editor ? $model->editor->full_name : null ;
                }
            ],
        ];
    }
}
