<?php

namespace App\Filters;

use App\Filters\TitleFilter;
use App\Filters\AuthorFilter;
use Illuminate\Database\Eloquent\Builder;

class FilterManager
{
    /**
     * Available filters and their corresponding strategy classes.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Initialize available filters with their respective strategy classes.
     */
    public function __construct()
    {
        $this->filters = [
            'title' => TitleFilter::class,
            'author_id' => AuthorFilter::class,
            'created_at' => PublishFilter::class,
            'tags' => TagFilter::class,
            'comment_count' => CommentFilter::class
            // Add other filters here as needed
        ];
    }

    /**
     * Apply the filters to the query.
     *
     * @param Builder $query
     * @param array $filterData
     * @return void
     */
    public function apply(Builder $query, array $filterData): void
    {
        foreach ($filterData as $filter => $value) {
            if (!array_key_exists($filter, $this->filters) || is_null($value)) {
                continue;
            }
            $filterClass = $this->filters[$filter];
            $filterInstance = app($filterClass);
            if ($filterInstance instanceof FilterStrategy) {
                $filterInstance->apply($query, $value);
            }
        }
    }
}
