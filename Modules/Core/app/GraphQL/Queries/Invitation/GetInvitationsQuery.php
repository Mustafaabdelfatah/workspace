<?php
namespace Modules\Core\GraphQL\Queries\Invitation;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\InvitationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class GetInvitationsQuery extends Query
{
    protected $attributes = [
        'name' => 'getInvitations',
    ];

    private InvitationRepository $repo;

    public function __construct(InvitationRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        return GraphQL::type('InvitationsResponse');
    }

    public function args(): array
    {
        return [
            'page' => [
                'type' => Type::int(),
            ],
            'per_page' => [
                'type' => Type::int(),
            ],
            'workspace_id' => [
                'type' => Type::int(),
            ],
            'search_key' => [
                'type' => Type::string(),
            ],
        ];
    }

    public function resolve($root, $args)
    {
        return $this->repo->getInvitations($args);

    }
}

