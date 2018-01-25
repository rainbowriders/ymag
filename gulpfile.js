var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    mix.sass('helpers.scss');

    /* ========= THEMES START =========== */
    mix.less([
        'light/core.less',
        'light/components.less',
        'light/pages.less',
        'light/menu.less',
        'light/responsive.less'
    ], 'public/css/admin_light.css');

    mix.less([
        'shadow/core.less',
        'shadow/components.less',
        'shadow/pages.less',
        'shadow/menu.less',
        'shadow/responsive.less'
    ], 'public/css/admin_shadow.css');

    mix.less([
        'light/core.less',
        'light/components.less',
        'light/pages.less',
        'light/menu_dark.less',
        'light/responsive.less'
    ], 'public/css/admin_dark_menu.css');
    /* ========= THEMES END =========== */


    /* ========= SCRIPTS START =========== */
    mix.scripts([
        'node_modules/angular/angular.js',
        'node_modules/angular-sanitize/angular-sanitize.js',
        'node_modules/angular-local-storage/dist/angular-local-storage.js',
        'node_modules/underscore/underscore.js'
    ], 'public/js/vendor.js', './');

    mix.scripts([
        'helpers.js',
        'app.js',
        'controllers/*.js',
        'directives/*.js',
        'services/*.js'
    ], 'public/js/app.js', 'resources/assets/javascript');

    mix.scripts([
        'jquery.min.js',
        'bootstrap.min.js',
        'detect.js',
        'fastclick.js',
        'jquery.slimscroll.js',
        //'jquery.blockUI.js',
        //'waves.js',
        //'jquery.nicescroll.js',
        // 'jquery.scrollTo.min.js',
        'jquery.core.js',
        'jquery.app.js',
        'bootstrap-datepicker.js',
        'x2js.js',
        'left-sidebar-animate.js'


    ], 'public/js/libs.js', 'public/assets/js');
    /* ========= SCRIPTS END =========== */
    
    
    /* ========= STYLES END =========== */
    mix.styles([
        'bootstrap.min.css',
        'icons.css',
        'bootstrap-datepicker.css',
        'bootstrap-datepicker.standalone.css',
        'bootstrap-datepicker3.css',
        'bootstrap-datepicker3.standalone.css'

    ], 'public/css/vendor.css', 'public/assets/css');
    /* ========= STYLES END =========== */

    mix.copy('public/assets/fonts', 'public/fonts');
    
    /* ========= CACHE BOOSTING START =========== */
    mix.version([
        'css/admin_light.css',
        'css/admin_shadow.css',
        'css/admin_dark_menu.css',
        'css/helpers.css',
        'js/app.js',
        'js/vendor.js'
    ]);
    /* ========= CACHE BOOSTING END =========== */
});
