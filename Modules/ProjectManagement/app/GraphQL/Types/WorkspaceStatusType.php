<?php

namespace Modules\ProjectManagement\App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class WorkspaceStatusType extends GraphQLType
{
    protected $attributes = [
        'name' => 'WorkspaceStatusType',
        'description' => 'Workspace completion status'
    ];

    public function fields(): array
    {
        return [
            'success' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Operation success status'
            ],
            'workspace' => [
                'type' => \GraphQL::type('ProjectWorkspace'),
                'description' => 'Workspace details'
            ],
            'is_complete' => [
                'type' => Type::boolean(),
                'description' => 'Whether workspace is complete'
            ],
            'missing_fields' => [
                'type' => Type::listOf(Type::string()),
                'description' => 'List of missing required fields'
            ]
        ];
    }
}
