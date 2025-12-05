<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Types\Country;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CountryType extends GraphQLType
{
    protected $attributes = [
        'name' => 'countryType',
        'description' => 'this type defines country type'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::string(),
            ],
            'name' => [
                'type' => GraphQL::type('Translatable'),
                'resolve' => function ($country) {
                    return $country->getTranslations('name');
                }
            ],
        ];
    }
}
