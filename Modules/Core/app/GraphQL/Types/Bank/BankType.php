<?php
namespace Modules\Core\GraphQL\Types\Bank;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
class BankType  extends GraphQLType
{
    protected $attributes = [
        'name' => 'Bank',
        'description' => 'A type for Bank',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'The ID of the record',
            ],
            'bank_name' => [
                'type' => \GraphQL::type('Translatable'),
                'description' => 'The name of the bank  in different languages',
                'resolve'     => function ($model){
                    return $model->bank_name ? $model->getTranslations('bank_name') : null ;
                }
            ],
            'bank_short_code' => [
                'type' => Type::string(),
                'description' => 'Full name of the writer with ID',
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
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation datetime of the device',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The creation datetime of the device',
            ],
        ];
    }
}
