<?php

namespace Modules\ProjectManagement\App\Pipelines\Filters;

use Closure;

/**
 * Company Filter - Currently commented out until Company module is ready
 * Uncomment this when company module is implemented
 */
class CompanyFilter
{
    public function handle($query, Closure $next)
    {
        // Uncomment when company module is ready
        // if (request()->has('company_id') && request()->company_id) {
        //     $query->where('company_id', request()->company_id);
        // }

        return $next($query);
    }
}
