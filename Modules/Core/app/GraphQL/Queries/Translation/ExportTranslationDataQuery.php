<?php
namespace Modules\Core\GraphQL\Queries\Translation;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Modules\Core\Repositories\TranslationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ExportTranslationDataQuery extends Query
{
    protected $attributes = [
        'name' => 'exportTranslationData',
    ];

    private $repo;
    public function __construct(TranslationRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('TranslationTemplateResponse');
    }

    public function args(): array
    {
        return [
            'search_key' => [
                'type' => Type::string()
            ],
        ];
    }

    public function resolve($root, $args)
    {
         return $this->repo->exportTranslationData($args);
    }
}
