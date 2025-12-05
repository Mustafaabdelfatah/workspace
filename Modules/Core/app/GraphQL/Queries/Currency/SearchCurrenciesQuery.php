<?php
namespace Modules\Core\GraphQL\Queries\Currency;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\CurrencyRepository;
use Rebing\GraphQL\Support\Query;

class SearchCurrenciesQuery extends Query
{
    protected $attributes = [
        'name' => 'searchCurrencies',
        'description' => 'A query to retrieve searchCurrencies',
    ];

    private CurrencyRepository $repo;

    public function __construct(CurrencyRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        return \GraphQL::type('CurrenciesResponse');
    }

    public function args(): array
    {
        return [
            'searchKey' => [
                'type' => Type::string(),
             ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->searchCurrencies($args);

    }
}
