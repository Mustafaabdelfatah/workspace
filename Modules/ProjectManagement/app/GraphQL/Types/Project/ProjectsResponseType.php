<?php
namespace Modules\ProjectManagement\App\GraphQL\Types\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ProjectsResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ProjectsResponse',
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
            'records' => [
                'type' => Type::listOf(GraphQL::type('Project')),
                'description' => 'Project records'
            ],
            'paging' => [
                'type' => GraphQL::type('Paging'),
                'description' => 'Pagination information'
            ],
        ];
    }
}
