<?php
namespace Modules\Core\GraphQL\Queries\UserSetting;

 use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\UserSettingRepository;
 use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;
use Validator;

class GetUserSettingDisplayNameQuery extends Query
{
    protected $attributes = [
        'name' => 'getUserSettingDisplayName'
    ];

    private $repo;
    public function __construct(UserSettingRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('UserSettingDisplayNameResponse');
    }

    public function args(): array
    {
        return [
            
        ];
    }

    public function resolve($root, array $args)
    {
        return $this->repo->getUserSettingDisplayName($args);
    }
}

