<?php


namespace Modules\Core\GraphQL\Mutations\User;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\UserRepository;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Facades\DB;

class ChangeUserAdminNonAdminMutation extends Mutation
{
    protected $attributes = [
        'name' => 'changeUserAdminNonAdmin',
        'description' => 'change user role to admin not admin'
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
                'description' => 'The ID of the user to activate or deactivate',
            ],
        ];
    }

    public function resolve($root, array $args)
    {
        if(auth()->user()->is_admin != 1 && false){
        return [
                'status' => false,
                'message' => __('lang_unauthorized'),
            ];
        }
             return $this->repo->changeUserAdminNonAdmin($args);
    }
}
