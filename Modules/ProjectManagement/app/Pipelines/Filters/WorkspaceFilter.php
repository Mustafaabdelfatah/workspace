<?php

namespace Modules\ProjectManagement\App\Pipelines\Filters;

use Closure;

class WorkspaceFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('workspace_id') && request()->workspace_id) {
            $query->where('workspace_id', request()->workspace_id);
        }

        return $next($query);
    }
}
