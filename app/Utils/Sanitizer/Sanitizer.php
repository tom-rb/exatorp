<?php

namespace App\Utils\Sanitizer;

use Illuminate\Support\Facades\Facade;

class Sanitizer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Rees\Sanitizer\Sanitizer::class;
    }
}
