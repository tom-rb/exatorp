<?php

namespace App\SelectionProcess;


use App\Model\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class HowDidYouHearOption extends Entity
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}