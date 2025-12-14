<?php

namespace Modules\ProjectManagement\App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pipeline\Pipeline;

class ProjectFilterPipeline
{
    protected $filters = [
        WorkspaceFilter::class,
        StatusFilter::class,
        ProjectTypeFilter::class,
        EntityTypeFilter::class,
        SearchFilter::class,
        OwnerFilter::class,
        DateRangeFilter::class,
        WorkspaceCompletionFilter::class,
    ];

    public function apply(Builder $query, array $filters): Builder
    {
        return app(Pipeline::class)
            ->send($query)
            ->through(
                collect($this->filters)->map(function ($filter) use ($filters) {
                    return new $filter($filters);
                })->toArray()
            )
            ->thenReturn();
    }
}
