<?php

namespace App\Team;

use App\Model\Entity;

use Silber\Bouncer\Database\Role;
use Illuminate\Database\Eloquent\Builder;

class Job extends Entity
{
    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = false;

    /**
     * Adds the attribute 'area_name' to the Job. With this, an additional
     * query to the areas table just to get its name is not needed (the
     * Eloquent eager load would do this extra query automatically).
     *
     * @param Builder $builder
     */
    public function scopeWithAreaName(Builder $builder)
    {
        Builder::prepareToAddSelect($builder);

        $builder->leftJoin('areas', 'areas.id', '=', 'jobs.area_id')
            ->addSelect('areas.name as area_name');
    }

    /**
     * Scope a query to only get Jobs of the given area.
     *
     * @param Builder $query
     * @param Area|integer $area
     * @return Builder
     */
    public function scopeByArea(Builder $query, $area)
    {
        if ($area instanceof Area)
            $area = $area->id;

        return $query->where('area_id', $area);
    }

    /**
     * The area of the job.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * The role of the job.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The job's name and description, if available.
     *
     * @return string
     */
    public function getFullDescriptionAttribute()
    {
        $full = $this->name;

        if ($this->description) $full = $full . ' - ' . $this->description;

        return $full;
    }
}
