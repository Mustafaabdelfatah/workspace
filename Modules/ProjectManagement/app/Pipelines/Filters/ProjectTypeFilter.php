<?php

namespace Modules\ProjectManagement\App\Pipelines\Filters;

use Closure;

class ProjectTypeFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('project_type') && request()->project_type) {
            $query->where('project_type', request()->project_type);
        }

        return $next($query);
    }
}
