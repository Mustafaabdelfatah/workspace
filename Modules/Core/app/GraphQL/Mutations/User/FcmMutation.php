<?php declare(strict_types=1);

namespace Modules\Core\GraphQL\Mutations\User;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\AuthRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Closure;

class FcmMutation extends Mutation
{
    protected $attributes = [
        'name' => 'FcmToken',
        'description' => 'this mutation defines user fcm'
    ];

    private AuthRepository $userRepository;

    public function __construct(AuthRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function type(): Type
    {
        return GraphQL::type('UsersResponse');
    }

    public function args(): array
    {
        return [
            'token' => [
                'type' => Type::string(),
            ],
            'agent'=> [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return $this->userRepository->storeFcmToken($args);
    }
}
