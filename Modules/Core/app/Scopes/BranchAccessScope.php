<?php

namespace Modules\Core\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BranchAccessScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        if ($user->canViewAllBranches()) {
            return;
        }

        
        $builder->whereIn(
            'branches.id',
            $user->branches()->withoutGlobalScope(BranchAccessScope::class)->pluck('branches.id')
        );
    }
}
