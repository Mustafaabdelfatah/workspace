<?php

namespace Modules\Core\GraphQL\Queries\City;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\CityRepository;
use Rebing\GraphQL\Support\Query;
use GraphQL;

class CitiesQuery extends Query
{
    protected $attributes = [
        'name' => 'cities'
    ];

    private CityRepository $repo;

    public function __construct(CityRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        return GraphQL::type('CitiesResponse');
    }

    public function args(): array
    {
        return [
            'searchKey' => [
                'type' => Type::string(),
             ],
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
        return $this->repo->getCities($args);

    }
}
