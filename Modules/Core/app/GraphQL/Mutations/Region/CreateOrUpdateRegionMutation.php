<?php

namespace Modules\Core\GraphQL\Mutations\Region;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\RegionRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class CreateOrUpdateRegionMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createOrUpdateRegion'
    ];

    protected $core_conn = 'core';

    private RegionRepository $repo;

    public function __construct(RegionRepository $repo)
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
                'type' => Type::int(),
            ],
            'name' => [
                'type' => \GraphQL::type('TranslatableInput'),
            ],
            'country' => [
                'type' => Type::string(),
            ]

        ];
    }

    public function resolve($root, $args)
    {
        if (!auth()->user()->hasPermission('human_resource.setting.' . (isset($args['id']) ? 'edit' : 'add'))) {
            return [
                'status' => false,
                'message' => __('lang_unauthorized'),
            ];
        }

        $validator = Validator::make($args, [
            'id' => 'nullable|exists:' . $this->core_conn . '.regions,id',
            'name' => 'required',
            'name.ar' => 'required|string|min:3',
            'name.en' => 'required|string|min:3',
            'country' => 'required'
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first(),
            ];
        }

        return $this->repo->createOrUpdateRegion($args);
    }
}
