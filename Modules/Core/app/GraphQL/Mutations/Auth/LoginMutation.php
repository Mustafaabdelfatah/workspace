<?php

declare(strict_types=1);

namespace Modules\Core\GraphQL\Mutations\Auth;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\Core\Repositories\AuthRepository;
use Illuminate\Support\Facades\Validator;

class LoginMutation extends Mutation
{
    protected $attributes = [
        'name' => 'login'
    ];

    private AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function type(): Type
    {
        return GraphQL::type('LoginResponse');
    }

    public function args(): array
    {
        return [
            'email' => [
                'type' => Type::string(),
            ],
            'otp' => [
                'type' => Type::string(),
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {

        $validator = Validator::make($args,[
            'email' => 'required',
            'otp' =>'nullable|digits:'.config('core.otp_length')
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => implode(' ', $validator->errors()->all()),
            ];
        }

        return $this->authRepository->login($args);
    }
}
