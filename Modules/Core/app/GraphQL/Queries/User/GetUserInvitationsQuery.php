<?php
namespace Modules\Core\GraphQL\Queries\User;

 use GraphQL\Type\Definition\Type;
 use Modules\Core\Repositories\UserRepository;
 use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Query;
class GetUserInvitationsQuery extends Query
{
    protected $attributes = [
        'name' => 'getUserInvitations',
    ];

    private $repo;
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('UserInvitationsResponse');
    }

    public function args(): array
    {
        return [
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
            'search_key' => [
                'name' => 'search_key',
                'type' => Type::string(),
            ],
        ];
    }

    public function resolve($root, array $args)
    {
        return $this->repo->getUserInvitations($args);
    }
}

