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
            'data' => [
                'type' => GraphQL::type('Project'),
            ],
        ];
    }
}