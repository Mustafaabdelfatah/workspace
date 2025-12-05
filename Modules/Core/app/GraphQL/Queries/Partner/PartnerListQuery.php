<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Queries\Partner;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\PartnerRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Closure;

class PartnerListQuery extends Query
{
    protected $attributes = [
        'name' => 'getPartnersList',
        'description' => 'this query defines partners list with paginate'
    ];

    private PartnerRepository $partnerRepository;

    public function __construct(PartnerRepository $partnerRepository)
    {
        $this->partnerRepository = $partnerRepository;
    }

    public function type(): Type
    {
        return GraphQL::type('partnerResponseType');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::string(),
            ],
            'name' => [
                'type' => Type::string(),
            ],
            'page' => [
                'type' => Type::int(),
            ],
            'per_page' => [
                'type' => Type::int(),
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->partnerRepository->getPartnerList($args);
    }
}
