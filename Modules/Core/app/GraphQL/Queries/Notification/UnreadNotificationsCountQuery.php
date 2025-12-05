<?php
namespace Modules\Core\GraphQL\Queries\Notification;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Repositories\NotificationRepository;
use Rebing\GraphQL\Support\Query;
use GraphQL\Type\Definition\Type;

class UnreadNotificationsCountQuery extends Query
{
    protected $attributes = [
        'name' => 'unreadNotificationsCount',
    ];

    private $repo;
    public function __construct(NotificationRepository $repo)
    {
        $this->repo = $repo;
    }
        public function type(): Type
    {
        return Type::nonNull(Type::int());
    }


    public function resolve($root, $args)
    {


        return $this->repo->unreadNotificationsCount($args);
    }
}
