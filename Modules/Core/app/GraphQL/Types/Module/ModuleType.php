<?php
namespace Modules\Core\GraphQL\Types\Module;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ModuleType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Module',
        'description' => 'A type'
    ];

    public function fields(): array
    {
        return [
           'id' => [
                'type' => Type::int(),
            ],
            'module_key' => [
                'type' => Type::string(),
            ],
            'is_enabled' => [
                'type' => Type::boolean(),
            ],
            'frontend_slug' => [
                'type' => Type::string(),
            ],
            'module_name' => [
                'type' => \GraphQL::type('Translatable'),
                'resolve'     => function ($model){
                    return $model->module_name ? $model->getTranslations('module_name') : null ;
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
                'description' => 'The creation date of the contract term',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The last update date of the contract term',
            ],
        ];
    }
}
