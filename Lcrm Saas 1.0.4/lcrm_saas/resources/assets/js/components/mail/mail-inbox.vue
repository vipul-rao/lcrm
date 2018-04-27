<template>
<div class="card">
    <div class="card-header bg-white">
        <h4>Inbox</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-6">
                <div class="mail-option">
                    <div class="btn-group border px-3 py-1">
                        <div class="custom-control custom-checkbox my-1">
                            <input type="checkbox" class="custom-control-input" id="checkall" v-model="selectedAll">
                            <label class="custom-control-label" for="checkall"></label>
                        </div>
                    </div>
                    <div class="btn-group border">
                        <a @click.prevent="loadMails" title="Refresh" href="#" class="btn mini tooltips">
                            <i class=" fa fa-refresh"></i>
                        </a>
                    </div>
                    <div class="btn-group hidden-phone border">
                        <a data-toggle="dropdown" href="#" class="btn mini blue">
                            More
                            <i class="fa fa-angle-down "></i>
                        </a>
                        <div class="dropdown-menu vertical_scroll">
                            <a class="dropdown-item" href="#" @click.prevent="markAsRead">
                                <i class="fa fa-pencil"></i> Mark as Read
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" @click.prevent="deleteSelected">
                                <i class="fa fa-trash-o"></i> Delete
                            </a>
                        </div>
                    </div>
                    <ul class="unstyled inbox-pagination">
                    </ul>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="mail-box-header" v-if="loaded">
                    <form method="post" role="form" class="float-sm-right mail-search" @submit.prevent="loadMails">
                        <div class="input-group">
                            <input type="text" v-model="data.query" class="form-control input-sm" name="search" placeholder="Search email">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="mail-box">
            <div class="table-responsive">
                <table class="table" id="inbox-check">
                    <tbody>
                        <tr data-messageid="1" class="unread" v-if="mail.sender" v-for="mail in filtered_mails" :key="mail.id" :class="{'read' : mail.read }">
                            <td class="inbox-small-cells">
                                <div class="checker">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" v-model="mail.selected" :id="'selected'+mail.id">
                                        <label class="custom-control-label" :for="'selected'+mail.id"></label>
                                    </div>
                                </div>
                            </td>
                            <td class="view-message hidden-xs">
                                <router-link :to="{ name: 'inbox', params: { id: mail.id } }">
                                    {{ mail.sender.full_name }} </router-link>
                            </td>
                            <td class="view-message ">
                                <router-link :to="{ name: 'inbox', params: { id: mail.id } }">{{ mail.subject }}</router-link>
                            </td>
                            <td class="view-message text-right">
                                <router-link :to="{ name: 'inbox', params: { id: mail.id } }">{{ mail.created_at }}
                                </router-link>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="email_count == 0">
                    No Mails
                </div>
            </div>
        </div>
    </div>
</div>
</template>
<script>
export default {
    //props: ['url'],

    data: function() {
        return {
            data: {
                query: '',
                page: 1,
            },
            mails: [],
            email_count: 0,
            url: null,
            loaded: false,
            selectedAll: false,
        }
    },

    computed: {
        selectedMails: function() {
            return this.mails.filter(function(item) {
                return item.selected;
            });
        },
        filtered_mails: function() {
            var self = this;
            return self.mails.filter(function(item) {
                var regex = new RegExp(self.data.query.trim().toLowerCase());
                var res = item.subject.toLowerCase().match(regex, "i");
                if (res != null) {
                    return item;
                }
            })
        }
    },

    methods: {
        init: function(response) {
            this.mails = response.data.received.map((item) =>{
                item.selected = false;
                return item;
            });
            this.email_count = response.data.received_count;

            //Look for select all checkbox
            this.$watch('selectedAll', function(selected) {
                this.updateRowsSelection(selected);
            });

            bus.$emit('emailsLoaded');

            this.loaded = true;
            this.selectedAll = false;
        },

        loadMails: function() {
            axios.get(this.url + '/received', this.data).then(response => {
                this.init(response);
            }, error => {

            });
        },

        deleteSelected: function() {
            var ids = this.selectedMails.map((item) =>{
                return item.id;
            });

            axios.post(this.url + '/delete', {
                ids: ids
            }).then(function() {
                this.loadMails();
            }.bind(this));
        },

        markAsRead: function() {
            var ids = this.selectedMails.map((item) =>{
                return item.id;
            });

            axios.post(this.url + '/mark-as-read', {
                ids: ids
            }).then(() => {
                this.loadMails();
            });

        },
        updateRowsSelection: function(status) {
            this.mails.forEach((item)=> {
                item.selected = status;
            });
        },

        selectAllRead: function() {
            this.updateRowsSelection(false);
            this.mails.forEach((item)=> {
                if (item.read) {
                    item.selected = true;
                }
            });
        },

        selectAllUnRead: function() {
            this.updateRowsSelection(false);
            this.mails.forEach((item)=> {
                if (!item.read) {
                    item.selected = true;
                }
            });
        },
    },

    mounted: function() {
        this.url = this.$parent.url;
        this.loadMails();
    },

    filters: {
        date: function(val) {
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
