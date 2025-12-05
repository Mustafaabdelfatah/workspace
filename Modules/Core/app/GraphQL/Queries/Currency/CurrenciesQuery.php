<?php

namespace Modules\Core\GraphQL\Queries\Currency;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\CurrencyRepository;
use Rebing\GraphQL\Support\Query;
use GraphQL;

class CurrenciesQuery extends Query
{
    protected $attributes = [
        'name' => 'currencies',
        'description' => 'A query to retrieve paginated currencies',
    ];

    private CurrencyRepository $repo;

    public function __construct(CurrencyRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        return GraphQL::type('CurrenciesResponse');
    }

    public function args(): array
    {
        return [
            'page' => [
                'type' => Type::int(),
             ],
            'perPage' => [
                'type' => Type::int(),
             ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->getCurrencies($args);

    }
}
