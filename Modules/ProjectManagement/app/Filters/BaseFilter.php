<?php

namespace Modules\ProjectManagement\App\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class BaseFilter
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    abstract public function apply(Builder $query): Builder;

    protected function hasValue(string $key): bool
    {
        return isset($this->request[$key]) && !empty($this->request[$key]);
    }

    protected function getValue(string $key)
    {
        return $this->request[$key] ?? null;
    }
}
