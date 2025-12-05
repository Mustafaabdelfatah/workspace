<?php
namespace Modules\Core\GraphQL\Types\Translation;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TranslationType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Translation',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
            ],
            'module' => [
                'type' => Type::string(),
            ],
            'key' => [
                'type' => Type::string(),
            ],
            'phrase' => [
                'type' => \GraphQL::type('Translatable'),
                'resolve' => function ($model) {
                    return $model->phrase ? $model->getTranslations('phrase') : null;
                }
            ],
            'created_at' => [
                'type' => Type::string(),
            ],
            'updated_at' => [
                'type' => Type::string(),
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
