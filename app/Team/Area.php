<?php

namespace App\Team;

use App\Model\Entity;

class Area extends Entity
{
    /**
     * Indicates if the model should be timestamped.
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Area constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        if (! array_key_exists('slug', $attributes))
            $attributes['slug'] = str_slugfy(array_get($attributes, 'name'));

        parent::__construct($attributes);
    }

    /**
     * All jobs of the area.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
