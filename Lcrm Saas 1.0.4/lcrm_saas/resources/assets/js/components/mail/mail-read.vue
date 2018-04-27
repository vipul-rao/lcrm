<template>
<div>
    <div class="card">
        <div class="card-body ">
            <div class="mail-header row">
                <div class="col-md-8">
                    <h4 class="pull-left">{{ email.subject }}</h4>
                </div>
                <div class="col-md-4">
                    <div class="compose-btn pull-right">
                        <router-link :to="{ name: 'reply' }" class="btn btn-sm btn-primary">
                            <i class="fa fa-reply"></i> Reply
                        </router-link>
                    </div>
                </div>
            </div>
            <div class="mail-sender">
                <div class="row">
                    <div class="col-md-12">
                        <strong>{{ email.sender.full_name}}</strong> to <strong>me</strong> at <strong>{{ email.created_at }}</strong>
                    </div>
                </div>
            </div>
            <div class="view-mail">
                {{ email.message }}
            </div>
            <hr />
            <div class="compose-btn pull-left">
                <router-link :to="{ name: 'reply'}" class="btn btn-sm btn-primary">
                    <i class="fa fa-reply"></i> Reply
                </router-link>
            </div>
        </div>
    </div>
    <router-view></router-view>
</div>
</template>
<script>
export default {
    props: [],

    data: function() {
        return {
            email: {
                subject: "",
                id: "",
                from: "",
                created_at: "",
                message: "",
                sender: {
                    full_name: ""
                }
            },
        }
    },

    methods: {
        getMail: function() {
            axios.get(this.url + '/' + this.$route.params.id + '/get').then(response => {
                this.email = response.data.email;
                bus.$emit('newMailNotification');
            }, error => {

            });
        },
    },

    mounted: function() {
        this.url = this.$parent.url;
        this.getMail();
    },

    filters: {
        dateFull: function(val) {
            return moment(val).format('MMM Do YYYY, h:mm:ss a');
        }
    }
}
</script>
