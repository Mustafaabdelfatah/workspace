<?php
namespace Modules\ProjectManagement\App\GraphQL\Mutations\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Models\Project;
use Illuminate\Support\Facades\Auth;

class UpdateProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateProject',
    ];

    public function type(): Type
    {
        return GraphQL::type('Project');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::int()),
            ],
            'input' => [
                'name' => 'input',
                'type' => GraphQL::type('ProjectUpdateInput'),
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
            throw new \Exception('No permission to update this project');
        }

        $input = $args['input'];

        $updateData = [];
        $fields = ['name', 'description', 'status', 'manager_id', 'start_date', 'end_date'];

        foreach ($fields as $field) {
            if (isset($input[$field])) {
                $updateData[$field] = $input[$field];
            }
        }

        $project->update($updateData);

        return $project->fresh()->load(['workspace', 'owner', 'manager']);
    }
}
