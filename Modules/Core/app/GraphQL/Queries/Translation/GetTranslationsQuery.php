<?php
namespace Modules\Core\GraphQL\Queries\Translation;

 use GraphQL\Type\Definition\Type;
 use Modules\Core\Repositories\TranslationRepository;
 use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Validator;

class GetTranslationsQuery extends Query
{
    protected $attributes = [
        'name' => 'getTranslations',
        'description' => 'A query to get Translation with pagination and filtering',
    ];

    private $repo;
    public function __construct(TranslationRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        // Return a custom type or default array
        return GraphQL::type('TranslationsResponse');
    }

    public function args(): array
    {
        return [
            'search_key' => [
                'name' => 'search_key',
                'type' => Type::string(),
                'description' => 'Search by part of name, email, mobile, or username',
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'description' => 'Page number for pagination',
                'defaultValue' => 1,
            ],
            'perPage' => [
                'name' => 'perPage',
                'type' => Type::int(),
                'description' => 'Number of items per page for pagination',
                'defaultValue' => 10,
            ],
        ];
    }

    public function resolve($root, array $args)
    {
        return $this->repo->getTranslations($args);
    }
}

