<?php

namespace App\Infrastructure\Providers;

use Blade;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Change default router names to portuguese
        \Route::resourceVerbs([
            'create' => 'criar',
            'edit' => 'editar',
        ]);

        // Set morph names so DB values can be decoupled from class paths
        Relation::morphMap([
            'Area' => \App\Team\Area::class,
            'Job' => \App\Team\Job::class,
            'Member' => \App\Members\Member::class,
            'Student' => \App\Students\Student::class,
        ]);

        // Datetime locale
        \Carbon\Carbon::setLocale(config('app.locale'));
        setlocale(LC_TIME, config('app.locale'), 'ptb', 'pt_BR.utf-8', 'pt_BR.iso-8859-1');
        \Carbon\Carbon::setUtf8(true);

        // Enable foreign keys in SQLite
        $connection = env('DB_CONNECTION', config('database.default'));
        $driver = config("database.connections.$connection.driver");
        if ($driver == 'sqlite') {
            $db = app()->make('db');
            $db->connection()->getPdo()->exec("pragma foreign_keys=1");
        }

        $this->defineMacros();
        $this->defineBladeDirectives();
    }

    /**
     * Define some global macros to Laravel macroable classes.
     */
    public function defineMacros()
    {
        /**
         * Generates a download response of a dynamically created text.
         */
        Response::macro('downloadText', function ($text, $fileName) {
            // TODO: see improvements, as in streamDownload()
            // https://github.com/laravel/framework/blob/b48fcb0e66cdefc530f5426c39f93187099d4913/src/Illuminate/Routing/ResponseFactory.php#L123)
            return Response::make(utf8_decode($text), 200, [
                'Content-Type' => 'text/plain; charset=ISO-8859-1',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $fileName),
            ]);
        });

        /**
         * Generates a JSON successful response with a custom message.
         */
        Response::macro('jsonSuccess', function ($message, array $data = []) {
            return Response::json(array_merge(
                compact('message'), $data
            ));
        });

        /**
         * Generates a JSON error response with a custom message.
         */
        Response::macro('jsonError', function ($error, $status = 500, array $data = []) {
            return Response::json(array_merge(
                compact('error'), $data
            ), $status);
        });

        /**
         * Adds the '*' selector if columns are not yet defined or prepend already
         * selected columns with table name to prevent ambiguity in the query.
         *
         * This macro is intended to be used in local query scopes of Eloquent
         * models that dynamically adds a column to the query.
         */
        Builder::macro('prepareToAddSelect', function($builder) {
            $table = $builder->getModel()->getTable();

            if (!isset($builder->getQuery()->columns))
                $builder->select($table.'.*');
            else {
                $builder->select(array_map(function ($col) use ($table) {
                    return mb_strpos($col, '.') === false
                        ? $table.'.'.$col
                        : $col;
                }, $builder->getQuery()->columns));
            }
        });
    }

    /**
     * Register some directives to enhance the template engine.
     */
    protected function defineBladeDirectives()
    {
        /*
         * Echo 'is-active' if current relative uri (with query) contains the given
         * expression. Useful to write css class names for active elements.
         *
         * @param string Path - can use wildcard 'example/*' or 'example?q=*'
         * @param string Text - if empty, will echo 'active'
         * @param mixed Negate - if not empty, will negate the pattern matching result
         */
        Blade::directive('active', function($arguments) {
            $arguments = explode(",", $arguments);
            $urlPattern = trim($arguments[0],"'\"");
            $echoed = count($arguments) > 1 ? trim($arguments[1],"'\"") : 'is-active';
            $negate = count($arguments) > 2;

            if ($negate)
                return '<?= url_is("'.$urlPattern.'") ? \'\' : \''.$echoed.'\' ?>';
            return '<?= url_is("'.$urlPattern.'") ? \''.$echoed.'\' : \'\' ?>';
        });

        /*
         * Check if the current route is the given named route
         *
         * @param string Name - named route
         */
        Blade::directive('route', function($expression) {
            return "<?php if(route({$expression}) == request()->getUri()): ?>";
        });

        /**
         * Formats an instance of Carbon datetime to a localized string.
         *
         *   @date ($datetime [,'%d de %B, %Y'])
         *
         * @param date   \Carbon\Carbon The date to format
         * @param format string         An optional strftime format (default is '%d de %B, %Y')
         */
        Blade::directive('date', function ($expression) {
            $arguments = explode(",", $expression);
            $datetime = $arguments[0];
            $format = count($arguments) > 1 ? implode(",", array_slice($arguments, 1)) : "'%d de %B, %Y'";

            return "<?php echo with($datetime)->formatLocalized($format); ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Load in non production environment
        if ($this->app->environment() == 'local') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            // Checking for env() directly because it's a local environment
            if (env('DEBUG_BAR', 'false') == 'true')
                $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }

        $this->registerEloquentFactory();
    }

    /**
     * Override of Database Service Provider method to create the Faker with current locale
     */
    protected function registerEloquentFactory()
    {
        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create(config('app.locale'));
        });

        $this->app->singleton(EloquentFactory::class, function ($app) {
            $faker = $app->make(FakerGenerator::class);

            return EloquentFactory::construct($faker, database_path('factories'));
        });
    }
}
