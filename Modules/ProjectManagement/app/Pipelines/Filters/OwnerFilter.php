<?php

namespace Modules\ProjectManagement\App\Pipelines\Filters;

use Closure;

class OwnerFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('owner_id') && request()->owner_id) {
            $query->where('owner_id', request()->owner_id);
        }

        return $next($query);
    }
}
