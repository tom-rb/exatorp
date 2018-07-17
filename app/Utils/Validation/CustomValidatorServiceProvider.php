<?php

namespace App\Utils\Validation;

use Illuminate\Support\ServiceProvider;

class CustomValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap custom validators for the application.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::resolver( function ($translator, $data, $rules, $messages) {
            return new CustomValidator($translator, $data, $rules, $messages);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
