<template>
    <div v-cloak class="box1">
        <div class="row">
            <!-- column -->
            <div class="col-sm-4 col-md-3">
                <div id="cnt-list">
                    <div class="input-group">
                        <span class="input-group-addon no-border no-bg"><i class="fa fa-search"></i></span>
                        <input type="text" class="form-control no-border no-bg" placeholder="Search" v-model="query">
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="list curs-point" v-for="template in templatefilter" :key="template.id"
                                 :class="{'active': template.selected }">
                                <div class="list-item pointer" @click="selectItem(template)">
                                    <div class="list-body">
                                        <h5 class="contact-name">{{ template.title }}</h5>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /column -->
            <!-- column -->
            <div class="col-sm-8 col-md-9">
                <div class="row">
                    <!--<div class="col-lg-12">-->
                        <div id="cnt-list-details" class="email">
                            <div class="row">
                                <div class="col-md-12">
                                    <a :href="createurl" class="btn btn-success">
                                        <i class="fa fa-plus fa-fw"></i> New Email Template
                                    </a>
                                </div>
                            </div>
                            <div v-if="item">
                                <div class="email-template">
                                    <div>
                                        <a class="btn btn-sm btn-danger pull-right" @click="deleteItem(item)" v-if="can_delete">
                                            <i class="fa fa-times"></i>   Delete</a>
                                        <a class="btn btn-sm btn-primary" v-show="!this.item.editing" @click="editItem(item)" v-if="can_write"><i class="fa fa-pencil"></i>   Edit</a>
                                        <a class="btn btn-sm btn-primary" v-show="this.item.editing" @click="doneEditing(item)">Done</a>
                                    </div>
                                    <div class="errors alert alert-danger" v-if="errors">
                                        <ul>
                                            <li v-for="key in errors" :key="key">
                                                {{ key }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- fields -->
                                        <div class="form-horizontal">
                                            <div class="form-group">
                                                <label class="col-sm-3 form-control-label">Title</label>
                                                <div class="col-sm-9">
                                                    <div class="form-group" v-show="!item.editing">{{item.title}}
                                                    </div>
                                                    <input type="text" class="form-control" v-show="item.editing" v-model="item.title">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3  form-control-label">Text</label>
                                                <div class="col-sm-9">
                                                    <div class="form-group" v-show="!item.editing">{{item.text}}
                                                    </div>
                                                    <textarea class="form-control resize_vertical" v-show="item.editing" v-model="item.text" rows="5"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- / fields -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!--</div>-->
                </div>
            </div>
            <!-- /column -->
        </div>
    </div>
</template>
<script>
export default {
    props: ['url'],

    data: function() {
        return {
            email_templates: [],
            can_write: true,
            can_delete: true,
            errors: false,
            query: "",
            item: {}
        }
    },

    methods: {
        loadEmalTemplates: function(id) {
            axios.get(this.url + 'data')
                .then(response => {
                    var emailtemplates = response.data.email_templates;
                    var item_index = "";
                    emailtemplates.forEach(function(item, index) {
                        item.editing = false;
                        if (item.id == id) {
                            item_index = index;
                        }
                    });
                    //to display the last template edited
                    if (item_index != "") {
                        this.item = emailtemplates[item_index];
                    } else {
                        this.item = $.extend(true, {}, emailtemplates[0]);
                        this.item.selected = true;
                    }
                    this.email_templates = emailtemplates;
                    // this.can_write= response.data.can_write;
                    // this.can_delete= response.data.can_delete;
                })
                .catch(error => {
                    console.log(error);
                });
        },

        updateSelection: function(status) {
            this.email_templates.forEach((item)=> {
                item.selected = status;
            });
        },

        deleteItem: function(item) {
            var confirm = window.confirm("Deleting email template " + item.title);
            if (confirm) {
                for(var i=0;i<this.email_templates.length;i++){
                    if(item.id===this.email_templates[i].id){
                        var index=i;
                        break;
                    }
                }
                this.email_templates.splice(index, 1)
                this.item = $.extend(true, {}, this.email_templates[0]);
                axios.delete(this.url + item.id)
                    .then(response => {

                    })
                    .catch(error => {

                    });
            }
        },

        selectItem: function(item) {
            this.errors = false;

            this.updateSelection(false);
            item.selected = true;
            this.item = $.extend(true, {}, item);
        },

        editItem: function(eitem) {
            eitem.editing = true;
            this.item.editing = true;
        },

        doneEditing: function(item) {
            axios.put(this.url + item.id + '/ajax', item)
                .then(response => {
                    this.item = response.data.emailTemplate;
                    this.loadEmalTemplates(item.id);
                    this.errors = false;
                    item.editing = false;
                })
                .catch(error => {
                    this.errors = error.response.data;
                });
        },
    },
    computed: {
        createurl: function() {
            return this.url + "create";
        },
        templatefilter: function() {
            var self = this;
            return self.email_templates.filter(function(item) {
                var regex = new RegExp(self.query.trim().toLowerCase());
                var res = item.title.toLowerCase().match(regex, "i");
                if (res != null) {
                    return item;
                }
            })
        }
    },
    mounted: function() {
        this.loadEmalTemplates();
        $('.scrollable').css('height', (window.innerHeight - 150));
    }
}
</script>
