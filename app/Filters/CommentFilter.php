<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class CommentFilter extends FilterStrategy
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
        $query->whereHas('comments', null, '=', (int) $value);
    }
}
