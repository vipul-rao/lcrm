let mix = require('laravel-mix')
let front = require('./front-end.mix.js')
let back = require('./back-end.mix.js')

/**
 * compile the frontend assets
 */
front()

/**
 * compile the backend assets
 */
back()

mix.disableSuccessNotifications()

if (mix.inProduction()) {
    mix.version()
}
