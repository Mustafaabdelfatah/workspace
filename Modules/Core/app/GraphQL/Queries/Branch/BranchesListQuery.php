<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Queries\Branch;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\BranchRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Closure;

class BranchesListQuery extends Query
{
    protected $attributes = [
        'name' => 'getBranchesList',
        'description' => 'this query defines branches list'
    ];

    private BranchRepository $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    public function type(): Type
    {
        return GraphQL::type('branchResponseType');
    }

    public function args(): array
    {
        return [
            'name' => [
                'type' => Type::string(),
            ],
            'country_id' => [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->branchRepository->getBranchesList($args);
    }
}
