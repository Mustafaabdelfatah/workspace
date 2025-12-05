<?php


namespace Modules\Core\GraphQL\Mutations\User;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\UserRepository;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Facades\Validator;

class SaveUserMutation extends Mutation
{
    protected $attributes = [
        'name' => 'saveUser'
        
    ];
    private UserRepository $repo;

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function type(): Type
    {
        return GraphQL::type('GeneralResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::int(),
            ],
            'photo'=> ['type' => GraphQL::type('Upload')],
            'email' => [
                'type' => Type::string(),
            ],
            'mobile' => [
                'type' => Type::string(),
            ],
            'name' => [
                'type' => GraphQL::type('TranslatableInput'),
            ],
        ];
    }

    public function resolve($root, array $args)
    {

        $validator = Validator::make($args,[
            'id' => 'nullable|exists:users,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5000',
            'email' => 'required|email|unique:users,email,' . ($args['id'] ?? null),
            'mobile' => 'required|string|max:255|unique:users,mobile,' . ($args['id'] ?? null),
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
        ]);
        if($validator->fails()){
            return [
                'status' => false,
                'message' => $validator->errors()->first()
            ];
        }

        return $this->repo->saveUser($args);
    }
}
