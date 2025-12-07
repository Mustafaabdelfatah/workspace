<?php

namespace Modules\ProjectManagement\App\Pipelines\Filters;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserPermissionFilter
{
    public function handle($query, Closure $next)
    {
        $user = Auth::user();

        if (!$user->is_admin) {
            $query->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('manager_id', $user->id)
                  ->orWhereHas('members', function($memberQuery) use ($user) {
                      $memberQuery->where('user_id', $user->id);
                  });
            });
        }

        return $next($query);
    }
}
