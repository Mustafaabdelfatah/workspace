<?php
namespace Modules\ProjectManagement\App\GraphQL\Queries\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Models\Project;
use Illuminate\Support\Facades\Auth;

class GetProjectByIdQuery extends Query
{
    protected $attributes = [
        'name' => 'project',
    ];

    public function type(): Type
    {
        return GraphQL::type('ProjectSingleResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $user = Auth::user();
        $project = Project::with(['workspace', 'owner', 'manager', 'tasks', 'members'])
                         ->findOrFail($args['id']);

        if (!$user->is_admin && !$project->hasMember($user)) {
            throw new \Exception('Access denied');
        }

        return [
            'data' => $project
        ];
    }
}
