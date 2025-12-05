<?php

namespace Modules\ProjectManagement\App\GraphQL\Types\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProjectStatsType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ProjectStats',
        'description' => 'Project statistics data'
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::string(),
                'description' => 'Project status'
            ],
            'count' => [
                'type' => Type::int(),
                'description' => 'Number of projects with this status'
            ],
            'total_days' => [
                'type' => Type::int(),
                'description' => 'Total days for all projects with this status'
            ],
            'avg_days' => [
                'type' => Type::float(),
                'description' => 'Average days for projects with this status'
            ],
        ];
    }
}
