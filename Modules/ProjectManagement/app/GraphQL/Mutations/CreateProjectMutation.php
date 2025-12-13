<?php

namespace Modules\ProjectManagement\App\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Services\ProjectService;

class CreateProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProject',
        'description' => 'Create a new project'
    ];

    public function type(): Type
    {
        return GraphQL::type('CreateProjectResponse');
    }

    public function args(): array
    {
        return [
            'input' => [
                'type' => Type::nonNull(GraphQL::type('CreateProjectInput')),
                'description' => 'Project creation input'
            ]
        ];
    }

    public function resolve($root, array $args)
    {
        $projectService = new ProjectService();
        return $projectService->createProject($args['input']);
    }
}
