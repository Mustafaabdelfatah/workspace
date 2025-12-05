<?php

namespace Modules\Core\GraphQL\Mutations\Module;

use Modules\Core\Repositories\ModulesRepository;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;


class EnableDisableModuleMutation extends Mutation
{
    protected $attributes = [
        'name' => 'enableDisableModule',
    ];
    private $repo;

    public function __construct(ModulesRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        return GraphQL::type('GeneralResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::id(),
                'description' => 'The ID of the group to edit (optional for creating new group)',
            ],
        ];
    }

    public function resolve($root, $args)
    {

        return $this->repo->enableDisableModule($args);
    }
}
