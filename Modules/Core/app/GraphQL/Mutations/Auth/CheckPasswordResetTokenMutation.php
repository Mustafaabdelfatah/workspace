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

class CheckPasswordResetTokenMutation extends Mutation
{
    protected $attributes = [
        'name' => 'checkPasswordResetToken',
    ];

    private AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function type(): Type
    {
        return GraphQL::type('CheckPasswordResetTokenResponse');
    }

    public function args(): array
    {
        return [
            'token' => [
                'type' => Type::string(),
            ]
        ];
    }

    public function resolve($root, array $args)
    {
        $validator =  Validator::make($args,[
            'token'=> 'required',
        ]);
        
        if($validator->fails()){
            return [
                'status'=> false,
                'message'=> $validator->errors()->first()
            ];
        }
        return $this->authRepository->checkPasswordResetToken($args);
    }
}
