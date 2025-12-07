<?php

namespace Modules\ProjectManagement\App\Pipelines\Filters;

use Closure;

class BuildingTypeFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('building_type') && request()->building_type) {
            $query->where('building_type', request()->building_type);
        }

        return $next($query);
    }
}
