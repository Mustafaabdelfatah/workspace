<?php
namespace Modules\Core\GraphQL\Types\City;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CitiesResponseType extends GraphQLType
{
    protected $attributes = [
        'name' => 'CitiesResponse',
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
            'paging' => [
                "type" => GraphQL::type("Paging"),
            ],
            'records' => [
                "type" => Type::listOf(GraphQL::type("City")),
            ]
        ];
    }
}
