<template>
    <div class="card">
        <div class="card-body ">
            <div class="mail-box-header">
                <h2>Sent ({{ mails.length }})</h2>
            </div>
            <div class="mail-box">
                <div class="table-responsive">
                    <table class="table" id="inbox-check">
                        <thead>
                            <tr>
                                <th>
                                    To
                                </th>
                                <th>
                                    subject
                                </th>
                                <th>
                                    Date
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-messageid="1" class="unread" v-for="mail in mails" v-if="mail.receiver">
                                <td class="view-message">
                                    <router-link :to="{ name: 'sent', params: { id: mail.id } }">
                                        {{ mail.receiver.full_name }}</router-link>
                                </td>
                                <td class="view-message">
                                    <router-link :to="{ name: 'sent', params: { id: mail.id } }">{{ mail.subject }}</router-link>
                                </td>
                                <td class="view-message">
                                    <router-link :to="{ name: 'sent', params: { id: mail.id } }">{{ mail.created_at }}
                                    </router-link>
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
    props: [],

    data: function() {
        return {
            mails: []
        }
    },

    methods: {
        loadSentMails: function() {
            axios.get(this.url + '/sent').then(response => {
                this.mails = response.data.sent;
            }, function(error) {

            });
        }
    },

    mounted: function() {
        this.url = this.$parent.url;
        this.loadSentMails();
    },

    filters: {
        date: function(val) {
            return moment(val).fromNow();
        }
    }
}
</script>
