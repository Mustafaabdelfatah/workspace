<?php
namespace Modules\Core\GraphQL\Queries\Translation;

 use GraphQL\Type\Definition\Type;
 use Modules\Core\Repositories\TranslationRepository;
 use Rebing\GraphQL\Support\Query;

class GetTranslationsJsonQuery extends Query
{
    protected $attributes = [
        'name' => 'getTranslationsJson',
        'description' => 'Fetches the JSON translations in Base64 format based on the header language'
    ];
    private $repo;
    public function __construct(TranslationRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return \GraphQL::type('TranslationFileٍResponse');
    }

    public function args(): array
    {
        return [
            'lang_key' => [
                'name' => 'lang_key',
                'type' => Type::string(),
                'description' => 'The language key to determine the file to return',
                'defaultValue' => 'en',
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->getTranslationsJson($args);
    }
}
