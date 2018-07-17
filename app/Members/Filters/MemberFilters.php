<?php

namespace App\Members\Filters;

use App\Model\ModelFilter;

class MemberFilters extends ModelFilter
{
    protected $filters = ['status'];

    /**
     * Filter a query to only include members with given approval status.
     */
    public function status($status)
    {
        switch ($status) {
            case 'antigos':
                return $this->builder->former();
            case 'esperando':
                return $this->builder->onHold();
            default:
                return $this->builder;
        }
    }
}