<?php
namespace Modules\Core\GraphQL\Types\Permission;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class GroupType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Group',
        'description' => 'A type representing a group with permissions'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
                'description' => 'The ID of the group'
            ],
            'name' => [
                'type' => GraphQL::type('Translatable'),
                'description' => 'The name of the group',
                'resolve'=> function($root){
                   return is_object($root) ? $root->getTranslations('name') : @$root['name'];
                }
            ],
            'group_key' => [
                'type' => Type::string(),
            ],
            'module' => [
                'type' => GraphQL::type('Module')
            ],
            'permissions' => [
                'type' => Type::listOf(GraphQL::type('Permission')),
                'description' => 'The permissions assigned to the group'
            ]
        ];
    }
}
