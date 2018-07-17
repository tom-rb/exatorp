<?php

namespace App\Model;


use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

/**
 * Filters, translate http request parameters to database query.
 *
 * Derived classes define the filters by declaring the protected
 * variable $filters. If a given filter is present in the
 * request, a method of the same name will be called.
 *
 * @package App\Model
 */
abstract class ModelFilter
{
    /**
     * Available filters
     *
     * @var array
     */
    protected $filters = [];

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * MemberFilters constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply the available filters from http query to the builder by calling
     * derived class methods. If no filter is present, a default callable
     * will be triggered to scope a default query.
     *
     * @param $builder
     * @param null|Callable $default Callable with parameter $builder
     * @return Builder
     * @internal param Builder $query
     */
    public function apply($builder, $default = null)
    {
        $this->builder = $builder;

        $found = false;
        foreach ($this->filters as $filter) {
            $method = camel_case($filter);
            if ($this->request->exists($filter) && method_exists($this, $method)) {
                $this->$method($this->request->$filter);
                $found = true;
            }
        }

        if (!$found && is_callable($default)) {
            call_user_func($default, $this->builder);
        }

        return $this->builder;
    }
}