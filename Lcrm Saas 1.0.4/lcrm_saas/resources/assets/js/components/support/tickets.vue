<template>
    <div class="card">
        <div class="card-header bg-white">
            <div v-if="ticket_count == 0">
                <h4>
                    No {{openTickets ? 'Open' : 'Closed' }} Tickets
                </h4>
            </div>
            <div v-else>
                <h4>{{openTickets ? 'Open' : 'Closed' }} Tickets</h4>
            </div>
        </div>
        <div class="card-body">
            <div class="mail-box-header float-sm-right" v-if="loaded">
                <form method="post" role="form" class="mail-search" @submit.prevent="loadTickets">
                    <div class="input-group">
                        <input type="text" v-model="data.query" class="form-control input-sm" name="search" placeholder="Search tickets">
                    </div>
                </form>
            </div>
            <div class="mail-option">
                <div class="btn-group border px-3 py-1">
                    <div class="custom-control custom-checkbox my-1">
                        <input type="checkbox" class="custom-control-input" id="checkall" v-model="selectedAll">
                        <label class="custom-control-label" for="checkall"></label>
                    </div>
                </div>
                <div class="btn-group border">
                    <a @click.prevent="loadTickets" title="Refresh" href="#" class="btn mini tooltips">
                        <i class=" fa fa-refresh"></i>
                    </a>
                </div>
                <div class="btn-group hidden-phone border">
                    <a data-toggle="dropdown" href="#" class="btn mini blue">
                        More
                        <i class="fa fa-angle-down "></i>
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#" @click.prevent="markAsSolved">
                            <i class="fa fa-pencil"></i> Mark as Solved
                        </a>
                        <div class="dropdown-divider"></div>
                        <div :class="{active : !openTickets}">
                            <a class="dropdown-item" href="#" @click.prevent="getTickets">
                                <i class="fa fa-trash-o"></i> Closed Tickets
                            </a>
                        </div>
                    </div>
                </div>
                <ul class="unstyled inbox-pagination">
                </ul>
            </div>
            <div class="mail-box">
                <div class="table-responsive">
                    <table class="table" id="inbox-check">
                        <tbody>
                            <tr data-messageid="1" class="unread" v-for="ticket in filtered_tickets" :key="ticket.id" :class="{'read' : ticket.read }">
                                <td class="inbox-small-cells">
                                    <div class="checker">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" v-model="ticket.selected" :id="'ticket'+ticket.id">
                                            <label class="custom-control-label" :for="'ticket'+ticket.id"></label>
                                        </div>
                                    </div>
                                </td>
                                <td class="view-message ">
                                    <router-link :to="'/s/tickets/'+ticket.id">{{ ticket.subject }}</router-link>
                                </td>
                                <td class="view-message ">
                                    <router-link :to="'/s/tickets/'+ticket.id">{{ ticket.creator.full_name }}</router-link>
                                </td>
                                <td class="view-message text-right">
                                    <router-link :to="'/s/tickets/'+ticket.id">{{ ticket.created_at }}</router-link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                data: {
                    query: '',
                    page: 1,
                },
                tickets: [],
                openTickets: true,
                ticket_count: 0,
                url: null,
                loaded: false,
                selectedAll: false,
            }
        },

        computed: {
            selectedTickets() {
                return this.tickets.filter(function (item) {
                    return item.selected;
                });
            },
            filtered_tickets() {
                var self = this;
                return self.tickets.filter( (item) => {
                    var regex = new RegExp(self.data.query.trim().toLowerCase());
                    var res = item.subject.toLowerCase().match(regex, "i");
                    if (res != null) {
                        return item;
                    }
                })
            }
        },

        methods: {
            init(response) {
                this.tickets = response.data.received.map((item, index) => {
                    item.selected = false;
                    return item;
                })

                this.ticket_count = response.data.received_count;

                //Look for select all checkbox
                this.$watch('selectedAll', (selected) => {
                    this.updateRowsSelection(selected);
                });

                this.loaded = true;
                this.selectedAll = false;
            },

            getTickets() {
                this.openTickets ? this.loadClosedTickets() : this.loadTickets()
            },

            loadTickets() {
                axios.get(this.url + '/tickets', this.data).then(response => {
                    this.init(response);
                    this.openTickets = true
                }, error => {

                });
            },

            loadClosedTickets() {
                axios.get(this.url + '/closed-tickets', this.data).then(response => {
                    this.init(response);
                    this.openTickets = false
                }, error => {

                });
            },

            markAsSolved() {
                if (!confirm('Are you sure that these tickets are solved?')) {
                    return;
                }

                var ids = this.selectedTickets.map(item => {
                    return item.id;
                });

                axios.post(this.url + '/mark-as-solved', {
                    ids: ids
                }).then((response) => {
                    if (response.data == 'success') {
                        toastr["success"]("Tickets updated");
                    }
                    this.loadTickets();
                });
            },
            updateRowsSelection(status) {
                this.tickets.forEach((item)=> {
                    item.selected = status;
                });
            },

            selectAllRead() {
                this.updateRowsSelection(false);
                this.tickets.forEach((item)=> {
                    if (item.read) {
                        item.selected = true;
                    }
                });
            },

            selectAllUnRead() {
                this.updateRowsSelection(false);
                this.tickets.forEach((item)=> {
                    if (!item.read) {
                        item.selected = true;
                    }
                });
            },

            search() {
                this.loadTickets();
            }
        },

        mounted() {
            this.url = this.$parent.url;
            this.loadTickets();
            bus.$on('newTicket', () => {
                this.loadTickets()
            });
        },

        filters: {
            date(val) {
                return moment(val).fromNow();
            }
        }
    }
</script>
<style>
    .read {
        background-color: whitesmoke;
        color: grey;
    }
</style>
