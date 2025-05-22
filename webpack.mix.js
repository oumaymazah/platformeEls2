const mix = require('laravel-mix');
// Compiler le JS
mix.js('resources/js/quizzes/quiz-monitor.js', 'public/js');

// Compiler le CSS
mix.postCss('resources/css/quizzes.css', 'public/css');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sourceMaps();
