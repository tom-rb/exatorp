<?php

namespace App\Model;


trait CanBeFiltered
{
    /**
     * Scope a query using the given filters. A default callable can be
     * used to specify a scope if no filters were present in the request.
     *
     * @param $query
     * @param $filters
     * @param null|Callable $default A callable that receives $query argument
     * @return mixed
     */
    public function scopeFilter($query, $filters, $default = null)
    {
        return $filters->apply($query, $default);
    }

    /**
     * Alias to the filter scope.
     */
    public function scopeFilterOrDefault($query, $filters, $default = null)
    {
        return $filters->apply($query, $default);
    }
}