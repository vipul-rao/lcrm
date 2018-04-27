<template>
    <div class="card">
        <div class="card-header bg-white">
            <h4>Create Ticket</h4>
        </div>
        <div class="card-body">
            <div>
                <form method="post" role="form" @submit.prevent="createTicket">
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
    export default {
        props: [],
        data() {
            return {
                btn_disable: false,
                data: {
                    subject: "",
                    message: ""
                },
            }
        },
        methods: {
            createTicket() {
                let data = this.data;

                if (data.subject.trim().length && data.message.trim().length) {
                    this.btn_disable = true;
                    axios.post(this.url + '/create', data).then(response => {
                        this.data.subject = "";
                        this.data.message = "";
                        this.btn_disable = false;
                        toastr["success"]("Ticket registered successfully");
                        bus.$emit('newTicket');
                    }).catch(response => {
                        toastr["error"]("Ticket sending failed");
                        this.btn_disable = false;
                    });
                } else {
                    toastr["error"]("Please fill the required fields");
                }
            },
        },
        mounted() {
            this.url = this.$parent.url;
        }
    }
</script>
