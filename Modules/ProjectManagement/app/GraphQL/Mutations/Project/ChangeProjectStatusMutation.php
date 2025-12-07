<?php
namespace Modules\ProjectManagement\App\GraphQL\Mutations\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ChangeProjectStatusMutation extends Mutation
{
    protected $attributes = [
        'name' => 'changeProjectStatus',
    ];

    public function type(): Type
    {
        return GraphQL::type('ChangeProjectStatusResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::nonNull(Type::string()),
            ],
        ];
    }

    public function rules(array $args = []): array
    {
        return [
            'id' => ['required', 'exists:projects,id'],
            'status' => ['required', 'in:planning,active,on_hold,completed,cancelled'],
        ];
    }

    public function resolve($root, $args)
    {
        $user = Auth::user();

        try {
            $project = Project::findOrFail($args['id']);

            if ($project->owner_id !== $user->id && !$user->is_admin) {
                return [
                    'status' => 'error',
                    'message' => 'No permission to change project status',
                    'record' => null
                ];
            }

            $project->update(['status' => $args['status']]);

            return [
                'status' => 'success',
                'message' => 'Project status changed successfully',
                'record' => $project->fresh()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to change project status: ' . $e->getMessage(),
                'record' => null
            ];
        }
    }
}
