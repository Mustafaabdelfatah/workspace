<?php
namespace Modules\Core\GraphQL\Mutations\User;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class SignoutMutation extends Mutation
{
    protected $attributes = [
        'name' => 'signout',
        'description' => 'user signout mutation'
    ];

    public function type(): Type
    {
        return GraphQL::type('GeneralResponse');
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo)
    {
        $user = Auth::user();

        if ($user) {
            // Revoke all tokens
            $tokens = $user->tokens;
            foreach ($tokens as $token) {
                $token->revoke();
            }

            return [
                'status' => True,
                'message' => __('signout_successfully'),
            ];
        }

        return [
            'status' => false,
            'message' => __('error_signout'),
        ];
    }
}
