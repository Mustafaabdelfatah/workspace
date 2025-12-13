<?php

namespace Modules\ProjectManagement\App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Modules\ProjectManagement\App\Services\ProjectService;

class UserTypesQuery extends Query
{
    protected $attributes = [
        'name' => 'userTypes',
        'description' => 'Get all user types'
    ];

    public function type(): Type
    {
        return Type::listOf(\GraphQL::type('UserTypeType'));
    }

    public function resolve($root, array $args)
    {
        $projectService = new ProjectService();
        return $projectService->getUserTypes();
    }
}
