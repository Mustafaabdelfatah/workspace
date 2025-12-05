<?php
namespace Modules\Core\app\GraphQL\Queries\Permission;

use GraphQL\Type\Definition\Type;
 use Modules\Core\Repositories\PermissionRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class PermissionsTreeQuery extends Query
{
    protected $attributes = [
        'name' => 'permissionsTree',
    ];
    private $repo;
    public function __construct(PermissionRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('PermissionsTree');
    }

    public function resolve($root, $args)
    {
        return  $this->repo->getPermissionsTree($args);

    }


}
