<?php

namespace Modules\ProjectManagement\App\GraphQL\Types\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DeleteProjectResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'DeleteProjectResponse',
        'description' => 'Response type for delete project mutation'
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
        ];
    }
}
