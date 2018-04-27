<template>
    <div>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Unread Emails">
            <i class="fa fa-fw fa-envelope-o text-primary"></i>
            <span v-if="total > 0" class="badge badge-success">{{ total }}</span>
        </a>
        <div class="dropdown-menu dropdown-messages table-striped mail_notification_dropdown vertical_scroll">
            <div class="dropdown-title text-center bg-primary text-white">
                You have {{ total }} new emails.
            </div>
            <div class="dropdown-item" v-for="item in notifications" :key="item.id">
                <a :href="getUrl(item)" class="message striped-col" @click.prevent="clicked(item)">
                    <div class="row">
                        <div class="col-12">
                            <div class="float-left">
                                <strong>{{ item.sender.full_name }}</strong>
                                <br> {{ item.subject }}
                                <br>
                                <small>{{ item.created_at | date }}</small>
                            </div>
                            <div class="float-right">
                                <span class="badge badge-success label-mini msg-lable">New</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="dropdown-footer text-center bg-primary">
                <a :href="inboxurl" class="text-white">View Messages</a>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['url'],

        data: function () {
            return {
                total: null,
                notifications: []
            }
        },
        computed: {
            inboxurl: function () {
                return this.url + "/mailbox#/m/inbox";
            }
        },
        methods: {
            loadNotifications() {
                axios.get(this.url + '/mailbox/all')
                    .then(response => {
                        this.total = response.data.total;
                        this.notifications = response.data.emails;
                    })
                    .catch(error => {});
            },
            getUrl(item) {
                return this.url + '/mailbox#/m/inbox/' + item.id;
            },
            clicked(item) {
                bus.$emit('readMail', item, this.getUrl(item));
            }
        },

        mounted() {
            this.loadNotifications();
            bus.$on('emailsLoaded', () => {
                this.loadNotifications();
            });
        },
        created() {
            bus.$on('newMailNotification', email => {
                this.loadNotifications();
            })
        },

        filters: {
            date: function (val) {
                return moment(val).fromNow();
            }
        }
    }
</script>
