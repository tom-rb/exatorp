<?php

namespace App\Administration\NiceArtisan;

use Route;
use Illuminate\Support\ServiceProvider;

class NiceArtisanServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Routes
        if (! $this->app->routesAreCached()) {
            Route::group([
                'middleware' => ['web'],
                'prefix' => config('commands.settings.route', 'niceartisan')
            ], function () {
                Route::get('/{option?}', 'App\Administration\NiceArtisan\NiceArtisanController@show')
                    ->name('niceartisan');
                Route::post('item/{class}', 'App\Administration\NiceArtisan\NiceArtisanController@command')
                    ->name('niceartisan.exec');
            });
        }
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}