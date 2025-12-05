<?php
declare(strict_types=1);
namespace Modules\Core\GraphQL\Queries\Positions;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use App\SOLID\Traits\AuthTraits;
use Modules\Core\Repositories\PositionRepository;

class ListPositions extends Query
{
    use AuthTraits;
    protected $attributes = [
        'name' => 'listPositions'
    ];
    private $repo;
    public function __construct(PositionRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('HRPositionsPageResponse');
    }

    public function args(): array
    {
        return [
            'pageno' => [
                "type" => Type::int()
            ],
            'pagesize' => [
                "type" => Type::int()
            ],
            'search_key' => [
                "type" => Type::string()
            ],
            'status' => [
                "type" => Type::string()
            ]
        ];
    }
    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields) {
        if(empty($this->checkAuth())) {
            return [
                'status' => FALSE,
                'message' => 'Sorry Authentication Failed....',
                'records' => [],
            ];
        }
        return $this->repo->listsPositions($args);
    }
}

