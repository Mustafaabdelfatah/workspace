<?php
namespace Modules\Core\GraphQL\Mutations\Bank;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\BankRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CreateOrUpdateBankMutation  extends Mutation
{
    protected $attributes = [
        'name' => 'createOrUpdateBank',
    ];

    private BankRepository $repo;

    public function __construct(BankRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        return \GraphQL::type('GeneralResponse'); // Assuming you have a ReligionType GraphQL type
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::id(),
                'description' => 'The ID of the Bank for updating.',
            ],
            'bank_name' => [
                'type' => GraphQL::type('TranslatableInput'),
                'description' => 'The Bank name in different languages.',
            ],
            'bank_short_code' => [
                'type' => Type::string(),
                'description' => 'The bank short code.',
            ],
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

        return $this->repo->createOrUpdateBank($args) ;
    }

}
