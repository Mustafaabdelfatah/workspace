<?php

namespace Modules\ProjectManagement\App\Pipelines\Filters;

use Closure;

class ParentProjectFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('parent_project_id') && request()->parent_project_id) {
            $query->where('parent_project_id', request()->parent_project_id);
        }

        return $next($query);
    }
}
