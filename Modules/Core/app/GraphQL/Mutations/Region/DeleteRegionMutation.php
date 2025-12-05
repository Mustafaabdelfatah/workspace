<?php
namespace Modules\Core\GraphQL\Mutations\Region;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\RegionRepository;
use Rebing\GraphQL\Support\Mutation;

class DeleteRegionMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteRegion',
    ];

    private RegionRepository $repo;

    public function __construct(RegionRepository $repo)
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
        return $this->repo->deleteRegion($args);

    }
}
