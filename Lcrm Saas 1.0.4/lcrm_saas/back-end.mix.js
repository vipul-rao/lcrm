let mix = require('laravel-mix')

let vendors = 'node_modules/'

let resourcesAssets = 'resources/assets/'
let srcCss = resourcesAssets + 'css/'
let srcJs = resourcesAssets + 'js/'
let srcSass = resourcesAssets + 'sass/'

let dest = 'public/'
let destFonts = dest + 'fonts/'
let destCss = dest + 'css/'
let destJs = dest + 'js/'

let paths = {
    'jquery': vendors + 'jquery/dist/',
    'fontawesome': vendors + 'font-awesome/',
    'material_design_icons': vendors + 'material-design-icons/',
    'popperjs': vendors + 'popper.js/dist/umd/',
    'select2': vendors + 'select2/dist',
    'moment': vendors + 'moment',
    'fullcalendar': vendors + 'fullcalendar/dist',
    'icheck': vendors + 'icheck/',
    'toastr': vendors + 'toastr/build/',
    'select2BootstrapTheme': vendors + 'select2-bootstrap-theme/dist',
    'c3': vendors + 'c3/',
    'datatables_bs': vendors + 'datatables.net-bs/',
    'lobibox': vendors + 'lobibox/',
    'jasny_bootstrap': vendors + 'jasny-bootstrap/dist/',
    'pusherJs': vendors + 'pusher-js/'
}

function back() {

    mix.copy(srcCss, destCss)

    mix.copy(paths.fontawesome+'css/font-awesome.min.css', destCss);
    mix.copy(paths.fontawesome + 'fonts', destFonts);

    // material-design-icons
    mix.copy(paths.material_design_icons + 'iconfont',destCss+'material_icons/');

    //icheck
    mix.copy(paths.icheck + 'skins/', 'public/css/icheck/');

    mix.copy(srcJs + 'jquery-jvectormap', destJs)
    mix.copy('node_modules/d3/d3.min.js', destJs)
    // Minify todolist.js
    mix.combine([srcJs + 'todolist.js'], destJs + 'todolist.js')

    /**
     * SASS Compilation
     */
    mix.sass(srcSass + 'app.scss', 'public/css/app.css')
    /**
     * JS Compilation
     */
    mix.js(srcJs + 'app.js', destJs + 'secure.js')

    // Copy images straight to public
    mix.copy('resources/assets/img', 'public/img', false)
    mix.copy('resources/assets/img/logo.png', 'public/uploads/site')
    mix.copy('resources/assets/img/thumb_logo.png', 'public/uploads/site')
    mix.copy('resources/assets/img/favicon.ico', 'public/uploads/site')
    mix.copy('resources/assets/img/user.png', 'public/uploads/avatar')

    //c3&d3 chart css and js files
    mix.copy(paths.c3 + 'c3.min.css', 'public/css')
    mix.copy(paths.c3 + 'c3.min.js', 'public/js')

    //lobibox
    mix.copy(paths.lobibox + 'dist/css/lobibox.min.css', 'public/css')
    mix.copy(paths.lobibox + 'dist/js/lobibox.min.js', 'public/js')

    // jasny-bootstrap
    mix.copy(paths.jasny_bootstrap + 'css/jasny-bootstrap.min.css', 'public/css')
    mix.copy(paths.jasny_bootstrap + 'js/jasny-bootstrap.min.js', 'public/js')

    //pusher-js
    mix.copy(paths.pusherJs + 'dist/web/pusher.min.js','public/js');

    //JS Libraries
    mix.scripts([
        "node_modules/pace-progress/pace.min.js",
        paths.jquery + "jquery.min.js",
        paths.popperjs + "popper.min.js",
        "node_modules/bootstrap/dist/js/bootstrap.min.js",
        paths.toastr + 'toastr.min.js',
        "node_modules/datatables.net/js/jquery.dataTables.js",
        "node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js",
        paths.select2 + "/js/select2.min.js",
        paths.icheck + 'icheck.min.js',
        paths.moment + '/min/moment.min.js',
        paths.fullcalendar + '/fullcalendar.min.js',
        srcJs + 'lcrm_app.js',
    ], 'public/js/libs.js')
}

/**
 * Export the backend asset compilation
 */
module.exports = back
