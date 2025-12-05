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

class ResetPasswordMutation extends Mutation
{
    protected $attributes = [
        'name' => 'resetPassword',
    ];

    private AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function type(): Type
    {
        return GraphQL::type('GeneralResponse');
    }

    public function args(): array
    {
        return [
            'token' => [
                'type' => Type::string(),
            ],
             'password' => [
                'type' => Type::string(),
            ],
             'password_confirmation' => [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, array $args)
    {
        $validator =  Validator::make($args,[
            'token'=> 'required|exists:password_resets',
            'password'=> 'required|min:8|confirmed',
        ]);

        if($validator->fails()){
            return [
                'status'=> false,
                'message'=> $validator->errors()->first()
            ];
        }
        return $this->authRepository->resetPassword($args);
    }
}
