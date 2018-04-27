<template>
    <div>
        <div class="card">
            <div class="card-body ">
                <div class="row">
                    <div class="col-12">
                        <div>
                            <button v-if="ticket.status=='open'" class="btn btn-primary pull-right" @click="markAsSolved">Mark as solved</button>
                            <div class="alert alert-warning text-center" v-else>
                                Solved
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mail-header row">
                    <div class="col-md-12">
                        <h4 class="pull-left">{{ ticket.subject }}</h4>
                    </div>
                </div>
                <div class="mail-sender" v-if="ticket.creator">
                    <div class="row">
                        <div class="col-md-8">
                            <strong>Created By</strong> : {{ticket.creator.full_name}}
                        </div>
                    </div>
                </div>
                <div class="view-mail">
                    {{ ticket.message }}
                </div>
                <hr />
                <reply-view :replies="ticket.replies?ticket.replies:[]"></reply-view>
                <div class="compose-btn m-t-20">
                    <router-link :to="{ name: 'tickets' }" exact class="btn btn-sm btn-warning">
                        <i class="fa fa-ticket"></i> Tickets
                    </router-link>
                    <router-link :to="{ name: 'ticket_reply'}" class="btn btn-sm btn-primary">
                        <i class="fa fa-reply"></i> Reply
                    </router-link>
                </div>
            </div>
        </div>
        <router-view></router-view>
    </div>
</template>
<script>
    import replyView from './ticket-reply-view'
    export default {
        props: [],
        components: {
            replyView
        },
        data() {
            return {
                ticket: {
                    subject: "",
                    id: "",
                    from: "",
                    created_at: "",
                    message: ""
                }
            }
        },

        methods: {
            getTicket() {
                axios.get(this.url + "/" + this.$route.params.id + "/get").then(
                    response => {
                        this.ticket = response.data.ticket;
                    }
                );
            },
            markAsSolved() {
                if (!confirm('Are you sure that the ticket is solved?')) {
                    return;
                }
                axios.put(this.url + "/" + this.$route.params.id + "/solve").then(
                    response => {
                        if (response.data.ticket.status == 'closed') {
                            toastr["success"]("Ticket is solved");
                            this.ticket.status = 'closed';
                        }
                    }
                ).catch(error => {
                    toastr["error"]("Something wrong");
                });
            }
        },

        mounted() {
            this.url = this.$parent.url;
            this.getTicket();
        },

        filters: {
            dateFull(val) {
                return moment(val).format("MMM Do YYYY, h:mm:ss a");
            }
        }
    };
</script>
