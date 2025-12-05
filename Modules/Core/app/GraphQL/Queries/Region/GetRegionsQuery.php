<?php

namespace Modules\Core\GraphQL\Queries\Region;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\RegionRepository;
use Rebing\GraphQL\Support\Query;
use GraphQL;

class GetRegionsQuery extends Query
{
    protected $attributes = [
        'name' => 'getRegions'
    ];

    private RegionRepository $repo;

    public function __construct(RegionRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        return GraphQL::type('RegionsResponse');
    }

    public function args(): array
    {
        return [
            'searchKey' => [
                'type' => Type::string(),
             ],
             'country'=>[
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
        return $this->repo->getRegions($args);

    }
}
