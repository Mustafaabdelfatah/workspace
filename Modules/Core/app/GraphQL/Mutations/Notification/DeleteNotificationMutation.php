<?php


namespace Modules\Core\GraphQL\Mutations\Notification;


use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\NotificationRepository;
use Modules\Core\Repositories\UserRepository;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Facades\Validator;

class DeleteNotificationMutation extends Mutation
{
    protected $attributes = [
        'name' => 'deleteNotification',
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
            'notification_id' => [
                'type' => Type::int(),
                'description' => 'The ID of the notification',
            ],
        ];
    }

    public function resolve($root, array $args)
    {
        $validator = Validator::make($args, [
            'notification_id' => 'required|integer|exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return ['status' => false, 'message' => $validator->errors()->first()];
        }

        return $this->repo->deleteNotification($args);
    }
}
