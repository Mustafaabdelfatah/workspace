<?php


namespace Modules\Core\GraphQL\Mutations\Notification;


use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\NotificationRepository;
use Modules\Core\Repositories\UserRepository;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Facades\Validator;

class UpdateLastNotificationsClickMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateLastNotificationsClick',
    ];
    private  $repo;

    public function __construct(NotificationRepository $repo)
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
         
        ];
    }

    public function resolve($root, array $args)
    {
        return $this->repo->updateLastNotificationsClick($args);
    }
}
