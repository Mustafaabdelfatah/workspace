<?php

namespace Modules\ProjectManagement\App\GraphQL\Types\Member;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ProjectMemberType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ProjectMember',
        'description' => 'A project member'
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The member ID'
            ],
            'project_id' => [
                'type' => Type::int(),
                'description' => 'The project ID'
            ],
            'user_id' => [
                'type' => Type::int(),
                'description' => 'The user ID'
            ],
            'role' => [
                'type' => Type::string(),
                'description' => 'The member role in the project'
            ],
            'user' => [
                'type' => GraphQL::type('User'),
                'description' => 'The user information',
                'resolve' => function($member) {
                    return $member->user;
                }
            ],
            'project' => [
                'type' => GraphQL::type('Project'),
                'description' => 'The project information',
                'resolve' => function($member) {
                    return $member->project;
                }
            ],
            'joined_at' => [
                'type' => Type::string(),
                'description' => 'When the user joined the project',
                'resolve' => function($member) {
                    return $member->created_at->format('Y-m-d H:i:s');
                }
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation timestamp',
                'resolve' => function($member) {
                    return $member->created_at->format('Y-m-d H:i:s');
                }
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The update timestamp',
                'resolve' => function($member) {
                    return $member->updated_at->format('Y-m-d H:i:s');
                }
            ],
        ];
    }
}
