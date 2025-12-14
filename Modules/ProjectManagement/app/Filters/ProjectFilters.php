<?php

namespace Modules\ProjectManagement\App\Filters;

use Illuminate\Database\Eloquent\Builder;

class WorkspaceFilter extends BaseFilter
{
    public function apply(Builder $query): Builder
    {
        if ($this->hasValue('workspace_id')) {
            $query->where('workspace_id', $this->getValue('workspace_id'));
        }

        return $query;
    }
}

class StatusFilter extends BaseFilter
{
    public function apply(Builder $query): Builder
    {
        if ($this->hasValue('status')) {
            $query->where('status', $this->getValue('status'));
        }

        return $query;
    }
}

class ProjectTypeFilter extends BaseFilter
{
    public function apply(Builder $query): Builder
    {
        if ($this->hasValue('project_type')) {
            $query->where('project_type', $this->getValue('project_type'));
        }

        return $query;
    }
}

class EntityTypeFilter extends BaseFilter
{
    public function apply(Builder $query): Builder
    {
        if ($this->hasValue('entity_type')) {
            $query->where('entity_type', $this->getValue('entity_type'));
        }

        return $query;
    }
}

class SearchFilter extends BaseFilter
{
    public function apply(Builder $query): Builder
    {
        if ($this->hasValue('search')) {
            $searchTerm = $this->getValue('search');
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%{$searchTerm}%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%{$searchTerm}%"])
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%");
            });
        }

        return $query;
    }
}

class OwnerFilter extends BaseFilter
{
    public function apply(Builder $query): Builder
    {
        if ($this->hasValue('owner_id')) {
            $query->where('owner_id', $this->getValue('owner_id'));
        }

        if ($this->hasValue('owner_only') && $this->getValue('owner_only')) {
            $query->where('owner_id', auth()->id());
        }

        return $query;
    }
}

class DateRangeFilter extends BaseFilter
{
    public function apply(Builder $query): Builder
    {
        if ($this->hasValue('start_date_from')) {
            $query->whereDate('start_date', '>=', $this->getValue('start_date_from'));
        }

        if ($this->hasValue('start_date_to')) {
            $query->whereDate('start_date', '<=', $this->getValue('start_date_to'));
        }

        if ($this->hasValue('end_date_from')) {
            $query->whereDate('end_date', '>=', $this->getValue('end_date_from'));
        }

        if ($this->hasValue('end_date_to')) {
            $query->whereDate('end_date', '<=', $this->getValue('end_date_to'));
        }

        return $query;
    }
}

class WorkspaceCompletionFilter extends BaseFilter
{
    public function apply(Builder $query): Builder
    {
        if ($this->hasValue('workspace_details_completed')) {
            $query->where('workspace_details_completed', $this->getValue('workspace_details_completed'));
        }

        return $query;
    }
}
