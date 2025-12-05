<?php
namespace Modules\Core\GraphQL\Queries\User;

 use GraphQL\Type\Definition\Type;
 use Modules\Core\Repositories\UserRepository;
 use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Query;
use Validator;

class GetModulesQuery extends Query
{
    protected $attributes = [
        'name' => 'getModules',
    ];

    private $repo;
    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('ModulesResponse');
    }

    public function args(): array
    {
        return [
           
        ];
    }

    public function resolve($root, array $args)
    {
        return $this->repo->getModules($args);
    }
}

