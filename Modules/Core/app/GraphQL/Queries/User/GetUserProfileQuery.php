<?php
namespace Modules\Core\GraphQL\Queries\User;

 use GraphQL\Type\Definition\Type;
 use Modules\Core\Repositories\UserRepository;
 use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Query;
use Validator;

class GetUserProfileQuery extends Query
{
    protected $attributes = [
        'name' => 'getUserProfile',
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
        return GraphQL::type('LoginResponse');
    }

    public function args(): array
    {
        return [
           
        ];
    }

    public function resolve($root, array $args)
    {
        return $this->repo->getUserProfile($args);
    }
}

