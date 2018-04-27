<template>
    <div>
        <div v-for="reply in replies" :key="reply.id" class="replies m-t-20">
            <div>
                <strong>
                    {{reply.creator.full_name}}
                </strong>
            </div>
            <div>
                {{reply.comment}}
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            replies: {
                required: true,
                type: Array
            }
        }
    }
</script>
