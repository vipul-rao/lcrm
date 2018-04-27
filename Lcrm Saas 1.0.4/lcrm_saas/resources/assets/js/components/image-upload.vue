<template>
<div class="col-md-12">
    <div class="image-upload image_upload thumbnail">
        <label class="holder">
            <img :src="oldImage" class="img-responsive" v-if="oldImage&&!src">
            <img :src="src" class="img-responsive" ref="img_preview" v-if="src">
            <input ref="file" type="file" :name="name" id="" class="form-control input" @change="imageAdded">
        </label>
    </div>
</div>
</template>
<script>
export default {
    props:{
        oldImage:{
            type:String
        },
        name:{
            required:true,
            type:String
        }
    },
    data(){
        return{
            src:null
        }
    },
    methods:{
        imageAdded(){
            let file = this.$refs.file
                if (file.files&&file.files[0]) {
                let reader = new FileReader()
                let source = this.$refs.img_preview
                reader.onload = (e)=> {
                    this.src=e.target.result
                };

                reader.readAsDataURL(file.files[0])
            }
        }
    }
}
</script>
<style lang="scss" scoped>
.input{
    display: none;
}
.holder{
    border:1px solid #ccc;
    min-height: 200px;
    min-width: 100%;
    cursor: pointer;
}
</style>
