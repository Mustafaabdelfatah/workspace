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
            'data' => [
                'type' => Type::listOf(GraphQL::type('Project')),
            ],
            'pagination' => [
                'type' => GraphQL::type('Paging'),
            ],
        ];
    }
}
