<template>
    <div>
        <div class="card">
            <div class="card-body">
                <form class="form-horizontal" @submit.prevent="uploadFile">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                <span class="btn btn-default btn-file"><span class="fileinput-new">Select file</span><span class="fileinput-exists">Change</span>
                <input type="file" name="..." ref="fileInput">
                </span>
                        <span class="fileinput-filename"></span>
                        <a href="#" class="close fileinput-exists import-cat" data-dismiss="fileinput">&times;</a>
                    </div>
                    <br>
                    <button class="btn btn-primary">Upload and Review</button>
                    <a :href="downloadurl" class="btn btn-primary">Download Template</a>
                </form>
                <h5 v-if="total" class="m-t-20">Imported : {{ completed.length }} / {{ total }}</h5>
                <div class="table-responsive">
                    <table class="table sales-team import-wrapper" v-if="total">
                        <thead>
                        <tr>
                            <th>
                                <label class="md-check">
                                    <input type="checkbox" v-model="selectedAll">
                                    <i class="primary"></i>
                                </label>
                            </th>
                            <th>Name</th>
                            <th>Product Type</th>
                            <th>Quantity on hand</th>
                            <th>Quantity available</th>
                            <th>Sale price</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in data" :class="{'alert-info':item.created}">
                            <td>
                                <label class="md-check" v-if="!item.created">
                                    <input type="checkbox" v-model="item.selected">
                                    <i class="primary"></i>
                                </label>
                            </td>
                            <td>
                                {{ item.product_name }}
                                <div class="errors alert alert-danger" v-if="item.errors">
                                    <ul>
                                        <li v-for="key in item.errors">
                                            {{ key }}
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>
                                {{ item.product_type }}
                            </td>
                            <td>
                                {{ item.quantity_on_hand }}
                            </td>
                            <td>
                                {{ item.quantity_available }}
                            </td>
                            <td>
                                {{ item.sale_price }}
                            </td>
                            <td>
                                {{ item.description }}
                            </td>
                            <td>
                                <select class="form-control import-width" name="category_id" data-placeholder="Select Category"
                                        v-model="item.category_id">
                                    <option :value="category.id" v-for="category in categories">{{ category.title }}</option>
                                </select>
                            </td>
                            <td>
                                <button v-if="!item.created" class="btn btn-primary btn-xs" @click="createRecord(item)">Create</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <a v-show="remaining.length > 0" :class="{ 'disabled': !selected.length }" href="" @click.prevent="createAll" class="btn btn-primary pull-right">Create Selected</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['url'],

        data: function() {
            return {
                data: [],
                categories: null,
                productTypes : [],
                product_type : [],
                selectedAll: false
            }
        },

        filters: {
            success: function(items) {
                return items.filter(function(item) {
                    return item.created;
                });
            },

            rejected: function(items) {
                return items.filter(function(item) {
                    return item.errors
                });
            }
        },

        computed: {
            completed: function() {
                return this.data.filter(function(item) {
                    return item.created;
                });
            },

            remaining: function() {
                return this.data.filter(function(item) {
                    return !item.created;
                });
            },

            total: function() {
                return this.data.length;
            },

            selected: function() {
                return this.data.filter(function(item) {
                    return item.selected;
                });
            },
            downloadurl: function() {
                return this.url + "download-template";
            }
        },

        methods: {
            init: function(res) {
                //Excel ROWS
                this.data = res.data.salesteams.map((item)=> {
                    item.created = false;
                    item.errors = false;
                    item.selected = false;
                    item.category_id = 1;
                    return item;
                });

                //Product category to be used for Dropdown
                this.categories = res.categories;

                //productTypes
                this.productTypes = res.productTypes;

                //status
                this.statuses = res.statuses;

                //Look for select all checkbox
                this.$watch('selectedAll', function(selected) {
                    this.updateRowsSelection(selected);
                });

                this.selectedAll = false;
            },

            updateRowsSelection: function(status) {
                this.data.forEach((item)=> {
                    item.selected = status;
                });
            },

            uploadFile: function() {

                var formData = new FormData();
                formData.append('file', this.$refs.fileInput.files[0]);

                axios.post(this.url + 'import', formData)
                    .then(res => {
                        this.init(res.data);
                    }).catch(err => {
                    alert(err.response.data);
                });
            },

            createRecord: function(item) {
                if (!item.created) {
                    var vm = this;
                    axios.post(this.url + 'ajax-store', item)
                        .then(function(response) {
                            item.created = true;
                            item.selected = false;
                            item.errors = null;
                        })
                        .catch(function(error) {
                            console.log(error);
                            item.errors = error;
                        });
                }
            },

            createAll: function() {
                this.selected.forEach(function(item) {
                    this.createRecord(item);
                }.bind(this));
            }
        }
    }
</script>
