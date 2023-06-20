<template>
    <div>
        <!-- container -->
        <div class="container-fluid ">
            <div class="form-group row">
                <div class="col-md-12">
                    <div class="row shadow-sm p-3 ">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control search" v-model="query_dms_or_name" placeholder="Nhập mã hoặc tên KH và nhấn enter...."
                               style="border-radius:30px;padding-left: 35px;padding-right:40px" @keyup.enter="fetchCustomer()">
                               <i @click="resetFilter()" class="fas fa-times reset-filter"></i>
                            <i class="fas fa-search fa-rotate-90 text-secondary"
                                style="position: absolute;top: 12px;left: 27px;">
                            </i>
                            <div class="text-right">
                                <button @click="resetFilter()" class="btn custom-reset"><i
                                    class="fas fa-spinner"></i></button>
    
                            </div>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button @click="showImportExcel()" class="btn btn-sm btn-success" style="height: 35px"> <i
                        class="fas fa-file-import mr-2"></i>Import Excel</button>
                <button v-if="is_staff == true" @click="showModal()" class="btn btn-sm btn-info"
                    style="height: 35px;width: 90px;">
                    + Tạo mới
                </button>
            </div>
            <div class="form-group text-center">
                <h4 class="text-uppercase font-weight-bold">Danh sách khách hàng </h4>
            </div>

            <div class="container-header">

                <table v-if="loading">
                    <tbody>
                        <tr v-for="index in 10">
                            <td v-for="column in 5" :class="'td-' + column"><span></span></td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="!loading">
                    <b-table responsive hover striped small :bordered="true" :filter="filter" :fields="fields" :items="customers"
                        :tbody-tr-class="rowClass">
                        <template #cell(index)="data">
                            {{ data.index + (paginate.current_page - 1) * perPage + 1 }}
                        </template>
                        <template #cell(user_id)="data">
                            <span v-if="data.item.user_id"> {{ data.item.user.name }} </span>
                        </template>
                        <template #cell(action)="data">
                            <div class="margin">
                                <button v-if="is_staff == true" class="btn btn-xs" style="margin-right: 10px;"
                                    @click="editCustomer(data.item)"><i class="fas fa-edit text-green" style="color: green;"
                                        title="Edit"></i></button>

                                <button class="btn btn-xs" @click="deleteCustomer(data.item.id)"><i
                                        class="fas fa-trash text-red" style="color: red;" title="Delete"></i></button>
                            </div>
                        </template>
                    </b-table>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-form-label-sm col-md-1" style="text-align: left" for="">Per
                                page:</label>
                            <div class="col-md-3">
                                <b-form-select size="sm" v-model="perPage" :options="pageOptions" @input="fetchCustomer()" >
                                </b-form-select>
                            </div>
                            <label class="col-form-label-sm col-md-1" style="text-align: left" for=""></label>
                            <div class="col-md-3">
                                <b-pagination size="sm" :total-rows="paginate.total" :per-page="perPage"
                                    v-model="page" @input="fetchCustomer()" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="dms_customer" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form @submit.prevent=addCustomer>
                                <div class="modal-header">
                                    <h4 class="modal-title">
                                        <span v-if="!edit">Thêm mới khách hàng</span>
                                        <span v-if="edit">Cập nhật khách hàng</span>
                                    </h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>(DMS Code)</label>
                                        <input v-model="customer.dms_code" class="form-control" id="dms_code"
                                            name="dms_code" placeholder="Nhập mã (DMS Code) ..."
                                            v-bind:class="hasError('dms_code') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('dms_code')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('dms_code') }}</strong></span>
                                    </div>
                                    <div class="form-group">
                                        <label>Tên khách hàng</label>
                                        <small class="text-danger">*</small>
                                        <input v-model="customer.name" class="form-control" id="name" name="name"
                                            placeholder="Nhập tên khách hàng ..."
                                            v-bind:class="hasError('name') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('name')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('name') }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label>Địa chỉ</label>
                                        <small class="text-danger">*</small>
                                        <input v-model="customer.address" class="form-control" id="address" name="address"
                                            placeholder="Nhập địa chỉ khách hàng ..." />

                                    </div>


                                </div>

                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        Đóng
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        Lưu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <CustomerExcel ref="customerExcel" :fetchCustomer="fetchCustomer"></CustomerExcel>
    </div>
</template>
<script>
import ViewSubmission from './ViewSubmission.vue';
import CustomerExcel from './CustomerExcel.vue';
export default {
    components: {
        ViewSubmission,
        CustomerExcel
    },
    data() {
        return {
            query_dms_or_name: '',
            token: '',
            pagesNumber: [],
            placeholderText: "Tìm kiếm ",
            is_staff: false,
            loading: false,
            edit: false,
            errors: {},
            customer: {
                id: '',
                dms_code: '',
                name: '',
                address: '',
            },
            pageOptions: [10, 50, 100, 500],
            filter: "",
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'user_id',
                    label: 'Nhân viên bán hàng',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'dms_code',
                    label: 'Mã khách hàng',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'name',
                    label: 'Tên khách hàng',
                    class: 'text-nowrap',
                    sortable: true,
                },

                {
                    key: 'address',
                    label: 'Địa chỉ',
                    class: 'text-nowrap',
                    sortable: true,
                },

                {
                    key: 'action',
                    label: 'Tùy chỉnh',
                    class: 'text-nowrap',
                }
            ],
            page: 1,
            perPage: 10,
            paginate: {
                current_page: 1,
                last_page: 1,
                total: 0,
            },
            customers: [],
            page_url_customer: "/api/optimize-customer",
            page_url_create_customer: '/api/customer_store',
            page_url_update_customer: '/api/customer_put',
            page_url_destroy_customer: '/api/customer_delete',


        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchCustomer();
    },
    methods: {
        fetchCustomer() {
            this.loading = true;
            const params = new URLSearchParams({
                page: this.page,
                perPage: this.perPage,
                query_dms_or_name: this.query_dms_or_name,
            });
            var page_url = this.page_url_customer + '?' + params.toString();
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {
                    this.customers = data.data.data;
                    this.is_staff = data.staff;
                    this.paginate = data.paginate;
                    this.loading = false;
                })
                .catch(err => {
                    console.log(err);
                    this.loading = false;
                });
        },
        addCustomer() {
            var page_url = this.page_url_create_customer;
            var page_url_update = this.page_url_update_customer;
            if (this.edit === false) {
                fetch(page_url, {
                    method: "POST",
                    body: JSON.stringify(this.customer),
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        // console.log(data.data.success)
                        if (data.data.success == 1) {
                            this.reset();
                            this.showMessage('success', 'Thêm thành công');
                            this.fetchCustomer();
                            $('#dms_customer').modal('hide');


                        } else {

                            this.errors = data.data.errors;
                            this.showMessage('error', 'Thêm mới không thành công');
                            this.fetchCustomer();
                            //this.reset();


                        }
                    })
                    .catch(err => {
                        this.loading = false;

                    });
            } else {
                //update
                fetch(page_url_update + "/" + this.customer.id, {
                    method: "PUT",
                    body: JSON.stringify(this.customer),
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {

                        if (data.data.success == 1) {

                            this.showMessage('success', 'Cập nhật thành công');
                            this.fetchCustomer();
                            $('#dms_customer').modal('hide');
                            //this.clearError();
                        } else {
                            this.errors = data.data.errors;
                            this.showMessage('error', 'Cập nhật không thành công');
                            this.fetchCustomer();
                            //this.reset();


                        }
                    })
                    .catch(err => console.log(err));
            }

        },
        deleteCustomer(id) {
            if (confirm('Bạn muốn xoá?')) {
                fetch(`${this.page_url_destroy_customer}/${id}`, {
                    method: 'delete',
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.data.success == 1) {
                            this.showMessage('success', 'Xoá thành công');
                            this.fetchCustomer();
                            this.reset();
                        } else {
                            this.errors = data.data.errors;
                            this.showMessage('error', this.errors.id);

                        }

                    });
            }
        },
        editCustomer(item) {
            this.edit = true;
            //console.log(this.errors,'lll');
            this.errors = {};
            this.customer.id = item.id;
            this.customer.dms_code = item.dms_code;
            this.customer.name = item.name;
            this.customer.address = item.address;
            $('#dms_customer').modal('show');
            //this.clearError();

        },
        reset() {
            this.customer.id = '';
            this.customer.dms_code = null;
            this.customer.name = '';
            this.customer.address = '';
        },
        showModal() {
            this.edit = false;
            //console.log('thu');
            this.errors = {};
            $('#dms_customer').modal('show');
            this.reset();
        },
        placeholder() {
            return this.placeholderText;
        },
        rowClass(item, type) {
            if (!item || type !== 'row') return
            if (item.status === 'awesome') return 'table-success'
        },
        showViewCustomer(customer) {
            this.$refs.viewSubmission.viewSubmission(customer);
        },
        showMessage(type, title, message) {
            if (!title)
                title = "Information";
            toastr.options = {
                positionClass: "toast-bottom-right",
                toastClass: this.getToastClassByType(type),

            };
            toastr[type](message, title);
            //this.reset()
        },
        getToastClassByType(type) {
            switch (type) {
                case "success":
                    return "toastr-bg-green";
                case "error":
                    return "toastr-bg-red";
                case "warning":
                    return "toastr-bg-yellow";
                default:
                    return "";
            }
        },
        hasError(fieldName) {
            return fieldName in this.errors;
        },
        getError(fieldName) {
            //console.log(fieldName+"="+ this.errors[fieldName][0]);
            return this.errors[fieldName][0];

        },
        clearError(event) {
            Vue.delete(this.errors, event.target.name);
            //  console.log(event.target.name);
        },
        showImportExcel() {
            this.$refs.customerExcel.showModal();
        },
        resetFilter() {
           this.query_dms_or_name = '';
           this.fetchCustomer();
        },
    },
    computed: {
        rows() {
            return this.customers.length;
        },
    }
}




</script>
<style lang="scss" scoped
>
@keyframes moving-gradient {
    0% {
        background-position: -250px 0;
    }

    100% {
        background-position: 250px 0;
    }
}

table {
    width: 100%;

    tr {
        border-bottom: 1px solid rgba(0, 0, 0, .1);

        td {
            height: 50px;
            vertical-align: middle;
            padding: 8px;

            span {
                display: block;
            }

            &.td-1 {
                width: 20px;

                span {
                    width: 20px;
                    height: 20px;
                }
            }

            &.td-2 {
                width: 50px;

                span {
                    background-color: rgba(0, 0, 0, .15);
                    width: 50px;
                    height: 50px;
                }
            }

            &.td-3 {
                width: 400px;

                // padding-right: 100px;
                span {
                    height: 12px;
                    background: linear-gradient(to right, #eee 20%, #ddd 50%, #eee 80%);
                    background-size: 500px 100px;
                    animation-name: moving-gradient;
                    animation-duration: 1s;
                    animation-iteration-count: infinite;
                    animation-timing-function: linear;
                    animation-fill-mode: forwards;
                }
            }

            &.td-4 {}

            &.td-5 {
                width: 100px;

                span {
                    background-color: rgba(0, 0, 0, .15);
                    width: 100%;
                    height: 30px
                }
            }
        }
    }
}
.reset-filter{
    position: absolute;
    right: 32px;
    z-index: 3;
    cursor: pointer;
    color: gray;
    top: 12px;
   visibility: hidden;
    opacity: 0;
    transition: opacity .3s ease-out, visibility .3s ease-out;
}
input.form-control.search:focus+.reset-filter {
   visibility: visible;
    opacity: 1;
}
.custom-reset {
    background: rgb(247, 247, 247);
    color: black;
    border: 0;
    border-radius: 30px !important;
    font-weight: 300;

    &:hover {
        color: #0071db;
    }
}
</style>


