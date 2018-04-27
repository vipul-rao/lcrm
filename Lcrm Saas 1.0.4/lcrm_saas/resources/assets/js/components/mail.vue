<template>
<section class="message">
    <div class="row">
        <div class="col-sm-4 col-md-4">
            <div class="card">
                <div class="pan">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <router-link to="/m/compose" role="button">COMPOSE</router-link>
                        </li>
                        <li class="list-group-item">
                            <router-link to="/m/inbox">
                                <span class="badge pull-right" v-if="email_count > 0">{{ email_count }}</span>
                                <i class="fa fa-inbox fa-fw mrs"></i> Inbox
                            </router-link>
                        </li>
                        <li class="list-group-item">
                            <router-link to="/m/sent">
                                <i class="fa fa-plane fa-fw mrs"></i> Sent Mail
                            </router-link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-sm-8 col-md-8">
            <router-view :key="$route.fullPath"></router-view>
        </div>
    </div>
</section>
</template>
<script>
export default {
    props: {
        url: {
            'required': true
        },
    },

    data: () => {
        return {
            email_count: 0,
            sent_email_count: 0,
            users: [],
            users_list: [],
        }
    },

    methods: {
        loadData: function() {

            axios.get(this.url + '/data', this.query).then(response => {
                this.email_count = response.data.email_count;
                this.sent_email_count = response.data.sent_email_count;
                this.users = response.data.users;
                this.users_list = response.data.users_list;
            }).catch(error => {

            });
        }
    },
    mounted() {
        this.loadData();
    }
}
</script>
