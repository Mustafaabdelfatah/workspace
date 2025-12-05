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
use Modules\Core\Models\Otp;

class RegisterMutation extends Mutation
{
    protected $attributes = [
        'name' => 'register'
    ];

    private AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function type(): Type
    {
        return GraphQL::type('RegisterResponse');
    }

    public function args(): array
    {
        return [
            'email' => [
                'type' => Type::string(),
            ],
            'mobile' => [
                'type' => Type::string(),
            ],
            'name' => [
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
            'email' => 'required|email|unique:users',
            'mobile' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'otp' =>'nullable|digits:'.config('core.otp_length')
        ]);
        if($validator->fails()){
            return [
                'status' => false,
                'message' => $validator->errors()->first()
            ];
        }
        return $this->authRepository->register($args);
    }
}
