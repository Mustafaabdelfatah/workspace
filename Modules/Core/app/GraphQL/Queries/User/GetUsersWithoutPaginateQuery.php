<?php
namespace Modules\Core\GraphQL\Queries\User;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\UserRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Query;
use Validator;

class GetUsersWithoutPaginateQuery extends Query
{
    protected $attributes = [
        'name' => 'getUsersWithoutPaginate',
        'description' => 'A query to get users without pagination and filtering',
    ];

    private $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        // Return a custom type or default array
        return GraphQL::type('UsersResponse');
    }

    public function args(): array
    {
        return [
            'search_key' => [
                'name' => 'search_key',
                'type' => Type::string(),
                'description' => 'Search by part of name, email, mobile, or username',
            ],
        ];
    }

    public function resolve($root, array $args)
    {
        // if(auth()->user()->is_admin != 1){
        // return [
        //         'status' => false,
        //         'message' => __('lang_unauthorized'),
        //     ];
        // }
        return $this->repo->getUsersWithoutPaginate($args);
    }
}
