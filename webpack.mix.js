const mix = require('laravel-mix');

mix.options({
    processCssUrls: false,
})
    .js('src/index.js', 'js/index.js')
    .setPublicPath('public');
