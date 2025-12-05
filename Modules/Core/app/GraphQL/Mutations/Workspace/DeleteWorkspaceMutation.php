<?php
namespace Modules\Core\GraphQL\Mutations\Workspace;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\WorkspaceRepository;
use Rebing\GraphQL\Support\Mutation;

class DeleteWorkspaceMutation  extends Mutation
{
    protected $attributes = [
        'name' => 'deleteWorkspace',
    ];

    private  $repo;

    public function __construct(WorkspaceRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return \GraphQL::type('GeneralResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int())
            ],
        ];
    }

    public function resolve($root, $args)
    {

        return $this->repo->deleteWorkspace($args);

    }
}

