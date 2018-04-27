<template>
    <section class="message">
        <div class="row">
            <div class="col-sm-12">
                <ticket-create v-if="showCreate"></ticket-create>
                <button class="btn btn-primary pull-right m-b-10" v-if="!isAdmin" @click.prevent="showCreate =!showCreate">New Ticket</button>
            </div>
            <div class="col-sm-12">
                <router-view :key="$route.fullPath"></router-view>
            </div>
        </div>
    </section>
</template>
<script>
    import tickets from './support/tickets'
    import ticketCreate from './support/ticket-create'
    export default {
        props: {
            url: {
                'required': true
            },
            isAdmin: {
                'required': false
            },
        },
        components: {
            tickets,
            ticketCreate
        },

        data() {
            return {
                showCreate: false,
            }
        },
        mounted() {
            bus.$on('newTicket', () => {
                this.showCreate = false
            });
        }
    }
</script>
