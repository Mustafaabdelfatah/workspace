<?php
namespace Modules\Core\GraphQL\Queries\Bank;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\BankRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class BanksQuery  extends Query
{
    protected $attributes = [
        'name' => 'banks',
    ];

    private BankRepository $repo;

    public function __construct(BankRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('BanksResponse');
    }

    public function args(): array
    {
        return [
            'page' => [
                'type' => Type::int(),
                'description' => 'The page number to retrieve',
            ],
            'perPage' => [
                'type' => Type::int(),
            ],
            'search_key' => [
                'type' => Type::string(),
             ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->getBanks($args);

    }
}
