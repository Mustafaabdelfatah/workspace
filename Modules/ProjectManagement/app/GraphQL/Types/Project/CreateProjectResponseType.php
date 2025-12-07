<?php

namespace Modules\ProjectManagement\App\GraphQL\Types\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CreateProjectResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CreateProjectResponse',
        'description' => 'Response type for create project mutation'
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Operation status (success/error)'
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Success or error message'
            ],
            'record' => [
                'type' => GraphQL::type('Project'),
                'description' => 'The created project record'
            ],
        ];
    }
}
