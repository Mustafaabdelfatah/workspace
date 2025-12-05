<?php
namespace Modules\ProjectManagement\App\GraphQL\Mutations\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Modules\ProjectManagement\App\Models\Project;
use Illuminate\Support\Facades\Auth;

class DeleteProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteProject',
    ];

    public function type(): Type
    {
        return Type::string();
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

    public function rules(array $args = []): array
    {
        return [
            'id' => ['required', 'exists:projects,id'],
        ];
    }

    public function resolve($root, $args)
    {
        $user = Auth::user();
        $project = Project::findOrFail($args['id']);

        if ($project->owner_id !== $user->id && !$user->is_admin) {
            throw new \Exception('No permission to delete this project');
        }

        $project->delete();

        return 'Project deleted successfully';
    }
}
