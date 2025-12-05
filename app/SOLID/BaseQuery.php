<?php

namespace App\SOLID;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Query;

abstract class BaseQuery extends Query
{
    abstract protected function checkAuthorization(array $args);

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        try {
            $this->checkAuthorization($args);
            return $this->executeQuery($args);
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    abstract protected function executeQuery(array $args);
}
