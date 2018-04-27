<template>
<div>
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-fw fa-bell-o black"></i>
            <span class="label label-warning" v-if="total > 0">{{ total }}</span>
        </a>
    <ul class="dropdown-menu dropdown-messages">
        <li class="dropdown-title">You have {{ total }} notifications</li>
        <li v-for="item in notifications">
            <a href="#" @click.prevent="readNotification(item)" class="message icon-not striped-col">
                    <i class="fa fa-fw fa-bell info"></i>
                    <div class="message-body">
                        <strong>{{ item.title }}</strong>
                        <br>
                        <span>{{ item.created_at | date }}</span>
                        <br>
                    </div>
                </a>
        </li>
    </ul>
</div>
</template>
<script>
export default {
    props: ['url'],


    data: function() {
        return {
            total: null,
            notifications: []
        }
    },


    methods: {
        loadNotifications() {
            axios.get(this.url + '/notifications/all')
                .then(response => {
                    this.total = response.data.total;
                    this.notifications = response.data.notifications;
                })
                .catch(function(error) {

                });
        },

        readNotification: function(item) {
            axios.post(this.url + '/notifications/read', {
                    id: item.id
                })
                .then(response => {
                    window.location = this.getUrl(item);
                }).catch(error => {
                    console.log('error in reading the notification...');
                });
        },

        getUrl: function(item) {
            return this.url + '/' + item.type + '/' + item.type_id + '/edit';
        }
    },

    filters: {
        date: function(val) {
            return moment(val).fromNow();
        }
    },

    mounted() {
        this.loadNotifications()
    },

    created() {
        bus.$on('newNotification', function(item) {
            this.loadNotifications();
        })

    }
}
</script>
