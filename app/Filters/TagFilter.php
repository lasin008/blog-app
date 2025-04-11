<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class TagFilter extends FilterStrategy
{
    /**
     * Apply the minimum price filter.
     *
     * @param Builder $query
     * @param mixed $value
     * @return void
     */
    public function apply(Builder $query, $value)
    {
        $query->whereHas('tags', function ($q) use ($value) {
            $q->whereIn('tags.id', (array) $value);
        });
    }
}
