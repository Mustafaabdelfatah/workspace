<?php
namespace Modules\Core\GraphQL\Mutations\Translation;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\TranslationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class DeleteTranslationMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteTranslation',
        'description' => 'A mutation to delete a Translation'
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
            'ids' => [
                'name' => 'ids',
                'type' => Type::nonNull(Type::listOf(Type::int())),
            ],
        ];
    }

    public function resolve($root, $args)
    {

        return $this->repo->deleteTranslation($args);
    }
}
