<?php
namespace Modules\Core\GraphQL\Mutations\City;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\CityRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
class CreateOrUpdateCityMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createOrUpdateCity'
    ];
    private CityRepository $repo;

    public function __construct(CityRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('GeneralResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
            ],
            'name' => [
                'type' => \GraphQL::type('TranslatableInput'),
            ],
            'nationality' => [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, $args)
    {
        if (!auth()->user()->hasPermission('human_resource.setting.'.(isset($args['id']) ? 'edit': 'add'))) {
            return [
                'status' => false,
                'message' => __('lang_unauthorized'),
            ];
        }

      return $this->repo->createOrUpdateCity($args);
    }
}
