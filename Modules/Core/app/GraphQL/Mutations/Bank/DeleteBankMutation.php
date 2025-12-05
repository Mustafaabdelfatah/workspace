<?php
namespace Modules\Core\GraphQL\Mutations\Bank;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\BankRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DeleteBankMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteBank',
        'description' => 'A mutation to delete a Bank'
    ];
    private BankRepository $repo;

    public function __construct(BankRepository $repo)
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
            'device_id' => [
                'name' => 'bank_id',
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
        return $this->repo->deleteBank($args);
    }
}
