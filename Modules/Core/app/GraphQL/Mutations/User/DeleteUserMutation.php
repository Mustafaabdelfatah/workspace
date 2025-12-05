<?php


namespace Modules\Core\GraphQL\Mutations\User;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\UserRepository;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DeleteUserMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteUser'
    ];
    private UserRepository $repo;

    public function __construct(UserRepository $repo)
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
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
            ],
        ];
    }

    public function resolve($root, array $args)
    {
        if(auth()->user()->is_admin != 1){
        return [
                'status' => false,
                'message' => __('lang_unauthorized'),
            ];
        }
             return $this->repo->deleteUser($args);
    }
}
