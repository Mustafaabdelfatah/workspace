<?php
namespace Modules\Core\GraphQL\Mutations\Translation;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\TranslationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class ImportTranslationDataMutation extends Mutation
{
    protected $attributes = [
        'name' => 'importTranslationData',
        'description' => 'Import translation data from an Excel file',
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
            'translation_template' => [
                'type' => GraphQL::type('Upload'), // Assuming you have file upload support
                'description' => 'The Excel file for translations',
                'rules' => ['required', 'mimes:xlsx,xls'],
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->importTranslationData($args);
    }
}
