<?php
namespace Modules\Core\GraphQL\Mutations\Translation;

use Modules\Core\Repositories\TranslationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;

class AddTranslationsMutation extends Mutation
{
    protected $attributes = [
        'name' => 'addTranslations',
        'description' => 'Add or update translations',
    ];
    
    private TranslationRepository $repo;

    public function __construct(TranslationRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        return GraphQL::type('GeneralResponse');
    }

    public function args(): array
    {
        return [
            'module' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'translations' => [
                'type' => Type::nonNull(Type::listOf(\GraphQL::type('PhraseInput'))),
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->addTranslations($args);
    }
}
