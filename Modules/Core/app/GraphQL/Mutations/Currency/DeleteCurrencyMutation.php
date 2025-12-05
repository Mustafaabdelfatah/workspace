<?php
namespace Modules\Core\GraphQL\Mutations\Currency;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\CurrencyRepository;
use Rebing\GraphQL\Support\Mutation;

class DeleteCurrencyMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteCurrency',
    ];

    private CurrencyRepository $repo;

    public function __construct(CurrencyRepository $repo)
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
                'type' => Type::nonNull(Type::int()),
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
        return $this->repo->deleteCurrency($args);

    }
}
