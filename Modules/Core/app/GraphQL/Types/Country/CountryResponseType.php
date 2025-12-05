<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Types\Country;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CountryResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'countryResponseType',
        'description' => 'this type defines country response type'
    ];

    public function fields(): array
    {
        return [
            'status' => [
                'type' => Type::boolean(),
            ],
            'message' => [
                'type' => Type::string(),
            ],
            'data' => [
                'type' => Type::listOf(GraphQL::type('countryType')),
            ],
        ];
    }
}
