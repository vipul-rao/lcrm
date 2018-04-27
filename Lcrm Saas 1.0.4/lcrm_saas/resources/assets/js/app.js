import Vue from 'vue'
import VueRouter from 'vue-router'
window.axios = require('axios')
/**
 * Add flatpickr as a framework independant date/time picker
 */
window.flatpickr = require("flatpickr").default
window.rangePlugin = require('flatpickr/dist/plugins/rangePlugin.js')

Vue.use(VueRouter)

window.axios.defaults.headers.common = {
    'X-CSRF-TOKEN': document.querySelector('#token').getAttribute('value'),
    'X-Requested-With': 'XMLHttpRequest'
}

//routing
const routes = [{
    path: '/m',
    component: require('./components/mail'),
    children: [{
        path: 'inbox',
        component: require('./components/mail/mail-inbox')
    }, {
        path: 'inbox/:id',
        name: 'inbox',
        component: require('./components/mail/mail-read'),
        children: [{
            path: 'reply',
            name: 'reply',
            component: require('./components/mail/mail-reply')
        }]
    }, {
        path: 'compose',
        component: require('./components/mail/mail-compose')
    }, {
        path: 'sent',
        component: require('./components/mail/mail-sent')
    }, {
        path: 'sent/:id',
        name: 'sent',
        component: require('./components/mail/mail-read-sent')
    }]
}, {
    path: '/s',
    component: require('./components/support'),
        children: [{
            path: 'tickets',
            name: 'tickets',
            component: require('./components/support/tickets'),
        },{
        path: 'tickets/:id',
        name:'view_tickets',
        component: require('./components/support/ticket-read'),
        children: [{
            path: 'reply',
            name: 'ticket_reply',
            component: require('./components/support/ticket-reply')
        }]
    }]
}]

const router = new VueRouter({
    routes,
    linkActiveClass: "active"
})

// Remove productionTip in console.
Vue.config.productionTip = false

//==routing
//=========global event bus=====
window.bus = new Vue()
//=========global event bus=====
const App = new Vue({
    router,
    components: {
        'contacts': require('./components/contacts'),
        'sales-team': require('./components/sales-team'),
        'customer-import': require('./components/customer-import'),
        'leads-import': require('./components/leads-import'),
        'category-import': require('./components/category-import'),
        'product-import': require('./components/product-import'),
        'company-import': require('./components/company-import'),
        'backup-settings': require('./components/backup-settings'),
        'notifications': require('./components/notifications'),
        'mail-notifications': require('./components/mail-notification'),
        'support-notifications': require('./components/support-notification'),
        'email-template': require('./components/email-template'),
        'image-upload': require('./components/image-upload')
    },

    methods: {
        initPusher: function () {
            /* Enable pusher logging - don't include this in production
               Pusher.log = function (message) {
               if (window.console && window.console.log) {
                    window.console.log(message)
               }
            }*/

            var pusherKey = document.querySelector('#pusherKey').getAttribute('value')
            var userId = document.querySelector('#userId').getAttribute('value')
            var pusher = new Pusher(pusherKey)
            //Channels
            var channel = pusher.subscribe('lcrm_channel.user_' + userId)

            /*Events

            channel.bind('App\\Events\\MeetingCreated', function (data) {
            toastr["success"]("New meeting scheduled: Subject - " + data.meeting.meeting_subject)
             })

           channel.bind('App\\Events\\CallCreated', function (data) {
            toastr["success"]("New call logged: Subject - " + data.call.call_summary)
           })

          channel.bind('App\\Events\\MailCreated', function (data) {
           toastr["success"]("New call logged: Subject - " + data.email.subject)
          })
         */

            channel.bind('App\\Events\\Email\\EmailCreated', function (data) {
                toastr["success"]("You got a new email")
                bus.$emit('newMailNotification', data.email)
            }.bind(this))

            channel.bind('App\\Events\\NotificationEvent', function (data) {
                toastr["success"](data.notification.title)
                bus.$emit('newNotification', data.notification)
            }.bind(this))
        },

        initToastr: function () {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "positionClass": "toast-top-right",
                "onclick": null,
                "showDuration": "1000",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            }
        }
    },


    mounted: function () {
        if (document.querySelector('#pusherKey')) {
            // this.initPusher()
        }
        this.initToastr()
        bus.$on('readMail', (item, link) => {
            this.$router.push({
                name: 'inbox',
                params: {
                    id: item.id
                }
            })
            window.location = link
        })
    }
}).$mount('#app')
