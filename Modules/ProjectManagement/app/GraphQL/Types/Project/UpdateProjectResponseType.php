<?php

namespace Modules\ProjectManagement\App\GraphQL\Types\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UpdateProjectResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'UpdateProjectResponse',
        'description' => 'Response type for update project mutation'
    ];

    public function fields(): array
    {
        return [
            'success' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Whether the operation was successful'
            ],
            'status' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Operation status (success/error)'
            ],
            'message' => [
                'type' => Type::string(),
                'description' => 'Success or error message'
            ],
            'project' => [
                'type' => GraphQL::type('Project'),
                'description' => 'The updated project record'
            ],
        ];
    }
}
