<?php

namespace Modules\ProjectManagement\App\Pipelines\Filters;

use Closure;

/**
 * Company Position Filter - Currently commented out until Company module is ready
 * Uncomment this when company module is implemented
 */
class CompanyPositionFilter
{
    public function handle($query, Closure $next)
    {
        // Uncomment when company module is ready
        // if (request()->has('company_position_id') && request()->company_position_id) {
        //     $query->where('company_position_id', request()->company_position_id);
        // }

        return $next($query);
    }
}
