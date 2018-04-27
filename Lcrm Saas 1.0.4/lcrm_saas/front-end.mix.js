let mix = require('laravel-mix')

/**
 * Define variables
 */

let vendors = 'node_modules/'
let resourcesAssets = 'resources/assets/front/'
let srcCss = resourcesAssets + 'css/'
let srcJs = resourcesAssets + 'js/'
let srcSass = resourcesAssets + 'sass/'

let dest = 'public/front/'
let destFonts = dest + 'fonts/'
let destCss = dest + 'css/'
let destJs = dest + 'js/'
let destVendors = dest + 'vendors/'

/**
 * Compilation
 */

let paths = {
    'jquery': vendors + 'jquery/dist/',
    'popperjs': vendors + 'popper.js/dist/umd/',
    'bootstrap': vendors + 'bootstrap/dist/',
    'fontawesome': vendors + 'font-awesome/',
    'animate': vendors + 'animate.css/',
    'textillate': vendors + 'textillate/',
    'isotope': vendors + 'isotope-layout/dist/',
    'wow': vendors + 'wow.js/',
    'imagesloaded': vendors + 'imagesloaded/',
    'gmap3': vendors + 'gmap3/dist/',
    'imagehover': vendors + 'imagehover.css/css/'
}

function front() {

    mix.copy(srcCss, destCss)
    mix.copy(srcJs, destJs)

    mix.copy(resourcesAssets + 'images', dest + 'images')

    //bootstrap
    mix.copy(paths.bootstrap + 'js/bootstrap.min.js', destJs)

    //popper
    mix.copy(paths.popperjs + 'popper.min.js', destJs)

    //fontawesome
    mix.copy(paths.fontawesome + 'css/font-awesome.min.css', destCss)
    mix.copy(paths.fontawesome + 'fonts', destFonts)

    //jquery
    mix.copy(paths.jquery + 'jquery.min.js', destJs)

    // Copy animate public
    mix.copy(paths.animate + 'animate.min.css', destVendors + 'animate.css/css')

    // Copy wow public
    mix.copy(paths.wow + 'css/libs/animate.css', destVendors + 'wow/css')
    mix.copy(paths.wow + 'dist/wow.min.js', destVendors + 'wow/js')

    //textillate
    mix.copy(paths.textillate + 'assets/animate.css', destVendors + 'textillate/css')
    mix.copy(paths.textillate + 'assets/jquery.lettering.js', destVendors + 'textillate/js')
    mix.copy(paths.textillate + 'jquery.textillate.js', destVendors + 'textillate/js')

    // Copy isotope public
    mix.copy(paths.isotope + 'isotope.pkgd.min.js', destVendors + 'isotope/js')

    //imageloaded
    mix.copy(paths.imagesloaded + 'imagesloaded.pkgd.js', destVendors + 'imagesloaded/js')

    // gmap3
    mix.copy(paths.gmap3 + 'gmap3.min.js', destVendors + 'gmap3/js')

    //copy imagehover public
    mix.copy(paths.imagehover + 'imagehover.min.css', destVendors + 'css')
    mix.copy(paths.imagehover + 'demo-page.css', destVendors + 'css')

    mix.sass(srcSass + 'bootstrap.scss', destCss + 'bootstrap.css')
    mix.sass(srcSass + 'custom.scss', destCss + 'custom.css')

    //-------------------mix-styles---------------------------

    mix.styles(
        [

            destCss + 'bootstrap.css',
            destCss + 'font-awesome.min.css',
            destCss + 'aboutstyles.css',
            destCss + 'blog_list_styles.css',
            destCss + 'contact_page1_styles.css',
            destCss + 'homepage.css',
        ], destCss + 'app.css')


    //-----------------------mix-scripts--------------------

    mix.scripts(
        [
            destJs + 'jquery.min.js',
            destJs + 'popper.min.js',
            destJs + 'bootstrap.min.js'
        ], destJs + 'app.js')

}
/**
 * Export the frontend asset compilation
 */
module.exports = front
