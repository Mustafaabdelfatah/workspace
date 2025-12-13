<?php

namespace Modules\ProjectManagement\App\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Modules\ProjectManagement\App\Models\ProjectInvitation;

class AcceptInvitationMutation extends Mutation
{
    protected $attributes = [
        'name' => 'acceptInvitation',
        'description' => 'Accept a project invitation'
    ];

    public function type(): Type
    {
        return Type::nonNull(Type::listOf(Type::string()));
    }

    public function args(): array
    {
        return [
            'token' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Invitation token'
            ]
        ];
    }

    public function resolve($root, array $args)
    {
        $invitation = ProjectInvitation::where('token', $args['token'])->first();

        if (!$invitation) {
            return [
                'success' => false,
                'message' => 'Invitation not found'
            ];
        }

        if ($invitation->accept()) {
            return [
                'success' => true,
                'message' => 'Invitation accepted successfully',
                'project' => $invitation->project
            ];
        }

        return [
            'success' => false,
            'message' => 'Unable to accept invitation'
        ];
    }
}
