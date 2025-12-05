<?php
namespace Modules\Core\GraphQL\Queries\Translation;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Modules\Core\Repositories\TranslationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class DownloadTranslationTemplateQuery extends Query
{
    protected $attributes = [
        'name' => 'downloadTranslationTemplate',
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

    public function resolve($root, $args)
    {
         return $this->repo->downloadTranslationTemplate();
    }
}
