<?php
namespace Modules\Core\GraphQL\Queries\Translation;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\TranslationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class GetTranslationUploadsHistoryQuery extends Query
{
    protected $attributes = [
        'name' => 'getTranslationUploadsHistory',
    ];

    private $repo;
    public function __construct(TranslationRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('TranslationsUploadsResponse');
    }

    public function args(): array
    {
        return [
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'defaultValue' => 1, // Set default value for page
            ],
            'perPage' => [
                'name' => 'perPage',
                'type' => Type::int(),
                'defaultValue' => 10, // Set default value for items per page
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->getTranslationUploadsHistory($args);
    }
}
