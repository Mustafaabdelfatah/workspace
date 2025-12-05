<?php
namespace Modules\ProjectManagement\App\GraphQL\Queries\Project;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Modules\ProjectManagement\App\Models\Project;
use Illuminate\Support\Facades\Auth;

class GetProjectsQuery extends Query
{
    protected $attributes = [
        'name' => 'projects',
    ];

    public function type(): Type
    {
        return GraphQL::type('ProjectsResponse');
    }

    public function args(): array
    {
        return [
            'filter' => [
                'name' => 'filter',
                'type' => GraphQL::type('ProjectFilterInput'),
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
                'defaultValue' => 1,
            ],
            'per_page' => [
                'name' => 'per_page',
                'type' => Type::int(),
                'defaultValue' => 20,
            ],
            'order_by' => [
                'name' => 'order_by',
                'type' => Type::string(),
                'defaultValue' => 'created_at',
            ],
            'order_dir' => [
                'name' => 'order_dir',
                'type' => Type::string(),
                'defaultValue' => 'DESC',
            ],
        ];
    }

    public function resolve($root, $args)
    {

        $user = Auth::user();
        $query = Project::query();

        if (isset($args['filter'])) {
            $filter = $args['filter'];

            if (isset($filter['workspace_id'])) {
                $query->where('workspace_id', $filter['workspace_id']);
            }

            if (isset($filter['status'])) {
                $query->where('status', $filter['status']);
            }

            if (isset($filter['owner_id'])) {
                $query->where('owner_id', $filter['owner_id']);
            }

            if (isset($filter['search'])) {
                $query->where(function($q) use ($filter) {
                    $q->where('name', 'LIKE', '%' . $filter['search'] . '%')
                      ->orWhere('code', 'LIKE', '%' . $filter['search'] . '%');
                });
            }
        }

        if (!$user->is_admin) {
            $query->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('manager_id', $user->id)
                  ->orWhereHas('members', function($memberQuery) use ($user) {
                      $memberQuery->where('user_id', $user->id);
                  });
            });
        }

        $orderBy = $args['order_by'] ?? 'created_at';
        $orderDir = $args['order_dir'] ?? 'DESC';
        $query->orderBy($orderBy, $orderDir);

        $perPage = $args['per_page'] ?? 20;
        $page = $args['page'] ?? 1;

        $paginator = $query->with(['workspace', 'owner', 'manager'])
                          ->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ]
        ];
    }
}
