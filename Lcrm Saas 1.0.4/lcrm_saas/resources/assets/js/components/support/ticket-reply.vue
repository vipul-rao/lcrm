<template>
    <div class="card">
        <div class="card-body">
            <textarea ref="commentBox" class="form-control resize_vertical" v-model="data.comment" rows="5" placeholder="Enter comment here..."></textarea>
            <div class="compose-btn pull-right mailv-reply">
                <a href="" @click.prevent="submitReply" class="btn btn-sm btn-primary m-t-10">Submit</a>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            data: {
                comment: "",
                url:"",
            }
        }
    },

    methods: {
        submitReply() {
            if (this.data.comment.trim().length) {
                axios.post(this.url + '/' + this.$route.params.id + '/comment', this.data).then(response => {
                    toastr["success"]("Success")
                    this.$router.push({
                        name: 'view_tickets',
                        params: {
                            id: this.$route.params.id
                        }
                    })
                }).catch(error=>{
                    toastr["error"]("Something wrong")
                })
            } else {
                toastr["error"]("Please fill all the required fields")
            }
        }
    },

    mounted() {
        this.url = this.$parent.$parent.url
        this.$nextTick(()=> {
            this.$refs.commentBox.focus()
        })
    }
}
</script>
