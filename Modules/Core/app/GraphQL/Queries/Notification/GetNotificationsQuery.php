<?php
namespace Modules\Core\GraphQL\Queries\Notification;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\NotificationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class GetNotificationsQuery extends Query
{
    protected $attributes = [
        'name' => 'getNotifications',
        'description' => 'A query to get Notification with pagination and filtering',
    ];

    private $repo;
    public function __construct(NotificationRepository $repo)
    {
        $this->repo = $repo;
    }
    public function type(): Type
    {
        // Return a custom type or default array
        return GraphQL::type('NotificationResponse');
    }

    public function args(): array
    {
        return [
            'unread_only' => [
                'name' => 'unread_only',
                'type' => Type::boolean(),
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'description' => 'Page number for pagination',
                'defaultValue' => 1,
            ],
            'perPage' => [
                'name' => 'perPage',
                'type' => Type::int(),
                'description' => 'Number of items per page for pagination',
                'defaultValue' => 10,
            ],
        ];
    }

    public function resolve($root, array $args)
    {
        return $this->repo->getNotifications($args);
    }
}
