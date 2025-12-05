<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Queries\Country;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\CountryRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Closure;

class CountriesListQuery extends Query
{
    protected $attributes = [
        'name' => 'getCountriesList',
        'description' => 'this query defines countries list'
    ];

    private CountryRepository $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function type(): Type
    {
        return GraphQL::type('countryResponseType');
    }

    public function args(): array
    {
        return [
            'name' => [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->countryRepository->getCountriesList($args);
    }
}
