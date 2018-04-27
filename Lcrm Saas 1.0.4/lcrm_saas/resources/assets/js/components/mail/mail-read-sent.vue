<template>
    <div class="card">
        <div class="card-body ">
            <div class="mail-header row">
                <div class="col-md-8">
                    <h4 class="pull-left">{{ email.subject }}</h4>
                </div>
            </div>
            <div class="mail-sender">
                <div class="row">
                    <div class="col-md-8">
                        <strong>me</strong> to
                        <strong>{{ email.receiver.full_name }}</strong> on <strong>{{ email.created_at }}</strong>
                    </div>
                </div>
            </div>
            <div class="view-mail">
                {{ email.message }}
            </div>
            <hr />
        </div>
    </div>
</template>
<script>
export default {
    props: [],
    data: function() {
        return {
            email: {
                subject: "",
                message: "",
                created_at: "",
                receiver:{
                    full_name:""
                }
            },
        }
    },

    methods: {
        getMail: function() {
            axios.get(this.url + '/' + this.$route.params.id + '/getSent').then(response => {
                this.email = response.data.email;
            }, function(error) {

            });
        },
    },

    mounted: function() {
        this.url = this.$parent.url;
        this.getMail();
    }
}
</script>
