<template>
<div class="card">
    <div class="card-header bg-white">
        <h4>Compose Message</h4>
    </div>
    <div class="card-body ">
        <div v-if="loaded">
            <form method="post" role="form" @submit.prevent="sendMail">
                <div class="form-group">
                    <label for="to" class="required">Recipients:</label>
                    <multiselect v-model="selectedusers" :options="users" track-by="text" label="text" :multiple="true" :close-on-select="false" :clear-on-select="false" :hide-selected="true" placeholder="Pick Recipients"></multiselect>
                </div>
                <div class="form-group" v-show="have_email_template">
                    <label for="email-template">Email Template:</label>
                    <multiselect v-model="emailTemplate" v-if="emailTemplates" :options="emailTemplates" track-by="text" label="text" placeholder="Pick an email-template"></multiselect>
                </div>
                <div class="form-group">
                    <label for="subject" class="required">Subject:</label>
                    <input type="text" class="form-control" v-model="data.subject" id="subject" />
                </div>
                <div class="form-group">
                    <label for="subject" class="required">Message:</label>
                    <textarea class="textarea tex-com form-control resize_vertical" v-model="data.message" rows="10" placeholder="Place some text here"></textarea>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-success" :disabled="btn_disable">
                                <span>Send</span>
                            </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</template>
<script>
import Vue from 'vue';
import Multiselect from 'vue-multiselect';
export default {
    props: [],
    components: {
        Multiselect
    },
    data: function() {
        return {
            users: null,
            emailTemplates: null,
            selectedusers: [],
            emailTemplate: "",
            btn_disable: false,
            data: {
                recipients: [],
                subject: "",
                message: ""
            },
            loaded: false,
            have_email_template: true
        }
    },
    watch: {
        emailTemplate(val) {
            if (val) {
                axios.get(this.url + '/mail-template/' + val.id).then(response => {
                    if (response.data.template == null) {
                        this.data.subject = ''
                        this.data.message = ''
                    } else {
                        this.data.subject = response.data.template.title
                        this.data.message = response.data.template.text
                    }
                }, function(error) {
                    this.data.subject = ''
                    this.data.message = ''
                });
            }
        }
    },

    methods: {
        sendMail: function() {
            var data = this.data;
            data.recipients = this.selectedusers.map(function(item) {
                return parseInt(item.id);
            });
            if (data.recipients.length != 0 && data.subject.trim().length && data.message.trim().length) {
                this.btn_disable = true;
                axios.post(this.url + '/send', data).then(response => {
                    this.data.subject = "";
                    this.data.message = "";
                    this.emailTemplate = ''
                    this.data.recipients = [];
                    this.btn_disable = false;
                    toastr["success"]("Email sent successfully");
                });
            } else {
                //alert('Please fill all the required fields.');
                toastr["error"]("Please fill the required fields");
            }
        },

        loadData: function() {
            var self = this;
            axios.get(this.url + '/data', this.query).then(response => {
                this.users = response.data.users;
                this.emailTemplates = response.data.email_templates;
                this.have_email_template = response.data.have_email_template;
                this.loaded = true;
            }, function(error) {

            });
        }
    },
    mounted: function() {
        this.url = this.$parent.url;
        this.loadData();
    }
}
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
