<?php

namespace Modules\ProjectManagement\App\Pipelines\Filters;

use Closure;

class CoordinatesFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('has_coordinates') && request()->has_coordinates) {
            $query->whereNotNull('latitude')->whereNotNull('longitude');
        }

        return $next($query);
    }
}
