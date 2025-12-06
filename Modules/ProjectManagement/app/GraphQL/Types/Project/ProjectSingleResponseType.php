<?php
namespace Modules\ProjectManagement\App\GraphQL\Types\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProjectSingleResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ProjectSingleResponse',
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::nonNull(Type::boolean()),
                'description' => 'Response status'
            ],
            'message' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Response message'
            ],
            'record' => [
                'type' => GraphQL::type('Project'),
                'description' => 'Project record'
            ],
        ];
    }
}
