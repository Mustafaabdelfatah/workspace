<?php
namespace Modules\Core\GraphQL\Mutations\Invitation;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\InvitationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Validator;

class AcceptInvitationMutation extends Mutation
{
    protected $attributes = [
        'name' => 'acceptInvitation'
    ];
    protected $core_connection;
    private  $repo;

    public function __construct(InvitationRepository $repo)
    {
        $this->core_connection = config('core.database_connection');
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('InvitationSingleResponse');
    }

    public function args(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string())
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->acceptInvitation($args);
    }
}

