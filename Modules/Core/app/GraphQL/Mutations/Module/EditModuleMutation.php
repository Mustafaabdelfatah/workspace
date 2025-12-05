<?php

namespace Modules\Core\GraphQL\Mutations\Module;

use Modules\Core\Repositories\ModulesRepository;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;


class EditModuleMutation extends Mutation
{
    protected $attributes = [
        'name' => 'editModule',
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
            'is_enabled' => [
                'type' => Type::boolean(),
                'description' => 'Name of the group',
            ],
            'frontend_slug' => [
                'type' => Type::string(),
                'description' => 'Name of the group key',
            ],
            'module_name' => [
                'type' => \GraphQL::type('TranslatableInput'),
                'description' => 'Module key associated with the group',
            ],
        ];
    }

    public function resolve($root, $args)
    {

        if(auth()->user()->is_admin != 1){
        return [
                'status' => false,
                'message' => __('lang_unauthorized'),
            ];
        }
        return $this->repo->editModule($args);
    }
}
