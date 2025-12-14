<?php

namespace Modules\ProjectManagement\App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Services\ProjectService;

class WorkspaceStatusQuery extends Query
{
    protected $attributes = [
        'name' => 'workspaceStatus',
        'description' => 'Get workspace completion status'
    ];

    public function type(): Type
    {
        return GraphQL::type('WorkspaceStatusType');
    }

    public function args(): array
    {
        return [
            'workspaceId' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'Workspace ID'
            ]
        ];
    }

    public function resolve($root, array $args)
    {
        $projectService = new ProjectService();
        return $projectService->getWorkspaceStatus($args['workspaceId']);
    }
}
