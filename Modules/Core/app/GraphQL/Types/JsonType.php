<?php
namespace Modules\Core\GraphQL\Types;

use GraphQL\Type\Definition\ScalarType;

class JsonType extends ScalarType
{
    public string $name = 'JSON';

    public function serialize($value)
    {
        return $value;
    }

    public function parseValue($value)
    {
        return $value;
    }

    public function parseLiteral(Node|\GraphQL\Language\AST\Node $valueNode, ?array $variables = null)
    {
        return json_decode($valueNode->value, true);
    }
}
