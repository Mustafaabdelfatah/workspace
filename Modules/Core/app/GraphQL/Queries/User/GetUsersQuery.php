<?php
namespace Modules\Core\GraphQL\Queries\User;

 use GraphQL\Type\Definition\Type;
 use Modules\Core\Repositories\UserRepository;
 use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Query;
use Validator;

class GetUsersQuery extends Query
{
    protected $attributes = [
        'name' => 'getUsers',
        'description' => 'A query to get users with pagination and filtering',
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
            'active_status' => [
                'name' => 'active_status',
                'type' => Type::boolean(),
                'description' => 'Filter by active status',
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'description' => 'Page number for pagination',
                'defaultValue' => 1,
            ],
            'perPage' => [
                'name' => 'perPage',
                'type' => Type::int(),
                'description' => 'Number of items per page for pagination',
                'defaultValue' => 10,
            ],
            'sort_order' => [
                'name' => 'sort_order',
                'type' => Type::string(),
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

        return $this->repo->getUsers($args);
    }
}
