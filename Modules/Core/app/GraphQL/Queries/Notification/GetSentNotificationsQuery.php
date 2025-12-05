<?php
namespace Modules\Core\GraphQL\Queries\Notification;

use GraphQL\Type\Definition\Type;
use Modules\Core\Repositories\NotificationRepository;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class GetSentNotificationsQuery extends Query
{
    protected $attributes = [
        'name' => 'getSentNotifications',
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
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'defaultValue' => 1,
            ],
            'perPage' => [
                'name' => 'perPage',
                'type' => Type::int(),
                'defaultValue' => 10,
            ],
        ];
    }

    public function resolve($root, array $args)
    {
        return $this->repo->getSentNotifications($args);
    }
}
