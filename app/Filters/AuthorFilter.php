<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class AuthorFilter extends FilterStrategy
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
        $query->where('author_id', '=', $value);
    }
}
