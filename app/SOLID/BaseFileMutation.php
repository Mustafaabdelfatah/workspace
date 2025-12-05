<?php

namespace App\SOLID;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Modules\Defualt\Repositories\FileRepository;
use Rebing\GraphQL\Support\Mutation;

abstract class BaseFileMutation extends Mutation
{
    abstract protected function checkAuthorization(array $args);

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        try {
            $this->checkAuthorization($args);
            return $this->executeMutation($args);
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    abstract protected function executeMutation(array $args);
}
