let mix  = require('laravel-mix');

// Front-end pipeline
mix.js('resources/assets/js/app.js', 'public/js')
    .extract(['vue', 'buefy', 'axios', 'urijs'])
    .sass('resources/assets/sass/app.sass', 'public/css');

// Activate BrowseSync
if (!mix.inProduction()) {
    mix.browserSync('http://127.0.0.1:8000')
}

// Apply version prefix to compiled css and js files (prevent browser cache in production)
if (mix.inProduction()) {
    mix.version();
}