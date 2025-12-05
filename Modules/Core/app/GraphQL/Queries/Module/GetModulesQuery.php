<?php
namespace Modules\Core\GraphQL\Queries\Module;

 use GraphQL\Type\Definition\Type;
 use Modules\Core\Repositories\ModulesRepository;
 use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class GetModulesQuery extends Query
{
    protected $attributes = [
        'name' => 'getSiteModules'
    ];

    private $repo;
    public function __construct(ModulesRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        // Return a custom type or default array
        return GraphQL::type('ModulesSiteResponse');
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
        ];
    }

    public function resolve($root, array $args)
    {
        return $this->repo->getModules($args);
    }
}

