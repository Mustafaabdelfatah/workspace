<?php
namespace Modules\Core\GraphQL\Mutations\Currency;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\CurrencyRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
class CreateOrUpdateCurrencyMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createOrUpdateCurrency',
        'description' => 'A mutation to create or update a currency'
    ];
    private CurrencyRepository $repo;

    public function __construct(CurrencyRepository $repo)
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
                'description' => 'The ID of the currency (optional for create)',
            ],
            'name' => [
                'name' => 'name',
                'type' => \GraphQL::type('TranslatableInput'),
                'description' => 'The name of the currency',
            ],
            'symbol' => [
                'name' => 'symbol',
                'type' => Type::string(),
                'description' => 'The symbol of the currency',
            ],
            'short_form' => [
                'name' => 'short_form',
                'type' => \GraphQL::type('TranslatableInput'),
                'description' => 'The short form of the currency',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        if (!auth()->user()->hasPermission('human_resource.setting.'.(isset($args['id']) ? 'edit': 'add'),'HRM')) {
            return [
                'status' => false,
                'message' => __('lang_unauthorized'),
            ];
        }

      return $this->repo->createOrUpdateCurrency($args);
    }
}
