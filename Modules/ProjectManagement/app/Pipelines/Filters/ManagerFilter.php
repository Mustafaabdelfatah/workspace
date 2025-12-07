<?php

namespace Modules\ProjectManagement\App\Pipelines\Filters;

use Closure;

class ManagerFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('manager_id') && request()->manager_id) {
            $query->where('manager_id', request()->manager_id);
        }

        return $next($query);
    }
}
