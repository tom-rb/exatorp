<?php

namespace App\Utils\Sanitizer;

use Rees\Sanitizer\Sanitizer;
use Illuminate\Support\ServiceProvider;

class SanitizerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap sanitizers for the application.
     */
    public function boot(Sanitizer $sanitizer)
    {
        $sanitizer->register('lowercase', function ($field) {
            return mb_strtolower($field);
        });

        $sanitizer->register('strip_spaces', function ($field) {
            return preg_replace(['/\s{2,}/', '/[\t\n]/'], ' ', trim($field));
        });

        $sanitizer->register('name_pt', function ($field) {
            return format_pt_name($field);
        });

        $sanitizer->register('array', function ($field) {
            return is_array($field) ? $field : [$field];
        });

        $sanitizer->register('bcrypt', function ($field) {
            return bcrypt($field);
        });
    }

    /**
     * Register the sanitizer provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Sanitizer::class, function ($app) {
            return new Sanitizer($app);
        });
    }
}
