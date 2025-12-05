<?php
namespace Modules\Core\GraphQL\Types\Currency;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CurrencyType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Currency',
        'description' => 'A Currency'
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
            'symbol' => [
                'type' => Type::string(),
             ],
            'short_form' => [
                'type' => \GraphQL::type('Translatable'),
                'resolve'     => function ($model){
                    return $model->short_form ? $model->getTranslations('short_form') : null ;
                }
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
