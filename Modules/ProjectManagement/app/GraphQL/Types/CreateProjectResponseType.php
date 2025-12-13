<?php

namespace Modules\ProjectManagement\App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CreateProjectResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CreateProjectResponse',
        'description' => 'Response from creating a project'
    ];

    public function fields(): array
    {
        return [
            'success' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Operation success status'
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Response message'
            ],
            'workspace_incomplete' => [
                'type' => Type::boolean(),
                'description' => 'Whether workspace is incomplete'
            ],
            'missing_fields' => [
                'type' => Type::listOf(Type::string()),
                'description' => 'List of missing workspace fields'
            ],
            'project' => [
                'type' => \GraphQL::type('Project'),
                'description' => 'Created project'
            ]
        ];
    }
}
