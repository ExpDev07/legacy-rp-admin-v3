const mix = require('laravel-mix');

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

// Version & source maps.
mix.version();
mix.sourceMaps();

// Assets.
mix.js('resources/js/app.js', 'public/js').vue();
mix.postCss('resources/css/app.pcss', 'public/css', [
    require('postcss-import'),
    require('tailwindcss'),
]);

// Config.
mix.webpackConfig({
    output: {
        chunkFilename: 'js/[name].js?id=[chunkhash]',
    },
});
