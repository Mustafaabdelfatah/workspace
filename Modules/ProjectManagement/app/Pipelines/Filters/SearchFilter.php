<?php

namespace Modules\ProjectManagement\App\Pipelines\Filters;

use Closure;

class SearchFilter
{
    public function handle($query, Closure $next)
    {
        if (request()->has('search') && request()->search) {
            $searchTerm = request()->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('code', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        return $next($query);
    }
}
