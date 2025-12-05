<?php
namespace Modules\Core\GraphQL\Mutations\Translation;

use Modules\Core\Repositories\TranslationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;

class UpdateTranslationsMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateTranslations',
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
            'id' => [
                'type' => Type::nonNull(Type::id()),
            ],
            'module' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'key' => [
                'type' => Type::nonNull(Type::string()),
            ],
            'phrases' => [
                'type' => \GraphQL::type('TranslatableInput'),
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->updateTranslation($args);
    }
}
