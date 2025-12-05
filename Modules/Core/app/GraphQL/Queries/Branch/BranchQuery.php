<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Queries\Branch;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\BranchRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Closure;

class BranchQuery extends Query
{
    protected $attributes = [
        'name' => 'getBranch',
        'description' => 'this query defines branch'
    ];

    private BranchRepository $branchRepository;

    public function __construct(BranchRepository $branchRepository)
    {
        $this->branchRepository = $branchRepository;
    }

    public function type(): Type
    {
        return GraphQL::type('branchSingleResponseType');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->branchRepository->getBranch($args);
    }
}
