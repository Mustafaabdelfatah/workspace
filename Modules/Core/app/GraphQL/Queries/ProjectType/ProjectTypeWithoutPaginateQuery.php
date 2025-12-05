<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Queries\ProjectType;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\ProjectTypeRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Closure;

class ProjectTypeWithoutPaginateQuery extends Query
{
    protected $attributes = [
        'name' => 'getProjectTypesWithoutPaginate',
        'description' => 'this query defines project type list without pagination'
    ];

    private ProjectTypeRepository $projectTypeRepository;

    public function __construct(ProjectTypeRepository $projectTypeRepository)
    {
        $this->projectTypeRepository = $projectTypeRepository;
    }

    public function type(): Type
    {
        return GraphQL::type('projectTypeResponseType');
    }

    public function args(): array
    {
        return [
            'search_key' => [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->projectTypeRepository->getProjectTypeWithoutPaginate($args);
    }
}
