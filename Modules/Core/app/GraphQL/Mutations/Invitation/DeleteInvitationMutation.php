<?php
namespace Modules\Core\GraphQL\Mutations\Invitation;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\InvitationRepository;
use Rebing\GraphQL\Support\Mutation;

class DeleteInvitationMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteInvitation',
    ];

    private  $repo;

    public function __construct(InvitationRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return \GraphQL::type('GeneralResponse');
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int())
            ],
        ];
    }

    public function resolve($root, $args)
    {

        return $this->repo->deleteInvitation($args);

    }
}

