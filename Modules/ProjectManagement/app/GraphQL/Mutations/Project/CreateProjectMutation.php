<?php
namespace Modules\ProjectManagement\App\GraphQL\Mutations\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Models\Project;
use Modules\Core\App\Models\Workspace;
use Illuminate\Support\Facades\Auth;

class CreateProjectMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createProject',
    ];

    public function type(): Type
    {
        return GraphQL::type('Project');
    }

    public function args(): array
    {
        return [
            'input' => [
                'name' => 'input',
                'type' => GraphQL::type('ProjectInput'),
            ],
        ];
    }

    public function rules(array $args = []): array
    {
        return [
            'input.workspace_id' => ['required', 'exists:workspaces,id'],
            'input.name' => ['required', 'string', 'max:255'],
        ];
    }

    public function resolve($root, $args)
    {
        $user = Auth::user();
        $input = $args['input'];

        $workspace = Workspace::findOrFail($input['workspace_id']);

        if (!$workspace->hasMember($user)) {
            throw new \Exception('No permission to create project in this workspace');
        }

        $project = Project::create([
            'workspace_id' => $input['workspace_id'],
            'name' => $input['name'],
            'code' => Project::generateCode($input['workspace_id']),
            'description' => $input['description'] ?? null,
            'status' => $input['status'] ?? 'planning',
            'owner_id' => $user->id,
            'manager_id' => $input['manager_id'] ?? null,
            'start_date' => $input['start_date'] ?? null,
            'end_date' => $input['end_date'] ?? null,
        ]);

        $project->addMember($user, 'owner');

        return $project->load(['workspace', 'owner']);
    }
}
