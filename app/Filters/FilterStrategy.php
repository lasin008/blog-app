<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class FilterStrategy
{
    /**
     * Apply the filter logic to the query builder.
     *
     * @param Builder $query
     * @param mixed $value
     * @return void
     */
    abstract public function apply(Builder $query, $value);
}
