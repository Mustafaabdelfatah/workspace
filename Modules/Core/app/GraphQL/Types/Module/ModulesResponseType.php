<?php
namespace Modules\Core\GraphQL\Types\Module;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ModulesResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ModulesSiteResponse'
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::boolean(),
                'description' => 'Indicates success or failure of the operation',
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Message about the operation',
            ],
            'paging' => [
                'type' => GraphQL::type('Paging'),
                'description' => 'Pagination information',
            ],
            'records' => [
                'type' => Type::listOf(GraphQL::type('Module')),
            ],
        ];
    }
}
