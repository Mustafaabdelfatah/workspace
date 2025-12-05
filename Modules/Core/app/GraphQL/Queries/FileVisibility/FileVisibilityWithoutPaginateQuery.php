<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Queries\FileVisibility;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\FileVisibilityRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Closure;

class FileVisibilityWithoutPaginateQuery extends Query
{
    protected $attributes = [
        'name' => 'getFileVisibilityWithoutPaginate',
        'description' => 'this query defines file visibility list without pagination'
    ];

    private FileVisibilityRepository $fileVisibilityRepository;

    public function __construct(FileVisibilityRepository $fileVisibilityRepository)
    {
        $this->fileVisibilityRepository = $fileVisibilityRepository;
    }

    public function type(): Type
    {
        return GraphQL::type('fileVisibilityResponseType');
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
        return $this->fileVisibilityRepository->getFileVisibilityWithoutPaginate($args);
    }
}
