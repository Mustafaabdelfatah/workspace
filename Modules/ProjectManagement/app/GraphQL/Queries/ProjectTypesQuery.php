<?php

namespace Modules\ProjectManagement\App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Modules\ProjectManagement\App\Services\ProjectService;

class ProjectTypesQuery extends Query
{
    protected $attributes = [
        'name' => 'projectTypes',
        'description' => 'Get all project types'
    ];

    public function type(): Type
    {
        return Type::listOf(\GraphQL::type('ProjectTypeType'));
    }

    public function resolve($root, array $args)
    {
        $projectService = new ProjectService();
        return $projectService->getProjectTypes();
    }
}
