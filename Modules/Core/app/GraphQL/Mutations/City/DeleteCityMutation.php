<?php
namespace Modules\Core\GraphQL\Mutations\City;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\CityRepository;
use Rebing\GraphQL\Support\Mutation;

class DeleteCityMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteCity',
    ];

    private CityRepository $repo;

    public function __construct(CityRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return \GraphQL::type('GeneralResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int())
            ],
        ];
    }

    public function resolve($root, $args)
    {
        if (!auth()->user()->hasPermission('human_resource.setting.delete','HRM')) {
            return [
                'status' => false,
                'message' => __('lang_unauthorized'),
            ];
        }
        return $this->repo->deleteCity($args);

    }
}
