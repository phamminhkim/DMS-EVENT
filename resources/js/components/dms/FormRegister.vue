<template>
    <div class="modal" id="form_register" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form @submit.prevent="addSubmission">
                    <div class="modal-header">
                        <h5 v-if="!edit" class="modal-title text-uppercase">Đăng ký tham gia chương trình</h5>
                        <h5 v-if="edit" class="modal-title text-uppercase">Chỉnh sửa tham gia chương trình</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="font-weight-bold text-sm">Chương trình</label>
                                    <small class="text-danger">*</small>

                                </div>
                                <div class="col-md-9">
                                    <treeselect v-model="submission.program_id" :multiple="false" :options="programs"
                                        id="program_id" name="program_id" placeholder="Chọn chương trình đăng ký...."
                                        v-bind:class="hasError('program_id') ? 'is-invalid' : ''" />
                                    <span v-if="hasError('program_id')" class="invalid-feedback" role="alert">
                                        <strong>{{ getError('program_id') }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="font-weight-bold text-sm">Khách hàng</label>
                                    <small class="text-danger">*</small>

                                </div>
                                <div class="col-md-9">
                                    <div v-if="!edit">
                                        <p class="options">
                                            <label class="mr-2">
                                                <input class="mr-2" type="radio" value="ALL" v-model="valueConsistsOf"
                                                    @change="handleSelectAll">Chọn tất cả</label>
                                            <label><input class="mr-2" type="radio" value="BRANCH_PRIORITY"
                                                    v-model="valueConsistsOf" @change="handleSelectNone">None</label>
                                        </p>
                                        <treeselect v-model="submission.customer_id" :multiple="true"
                                            :options="customerOptions" :value-consists-of="valueConsistsOf" id="customer_id"
                                            name="customer_id" placeholder="Chọn khách hàng tham gia...."
                                            v-bind:class="hasError('customer_id') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('customer_id')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('customer_id') }}</strong>
                                        </span>
                                    </div>

                                    <div v-if="edit">
                                        <treeselect v-model="submission.edit_customer_id" :multiple="false"
                                            :options="customerOptions" placeholder="Chọn khách hàng tham gia...." id="customer_id"
                                            name="customer_id" v-bind:class="hasError('customer_id') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('customer_id')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('customer_id') }}</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="font-weight-bold text-sm">Ghi chú</label>
                                </div>
                                <div class="col-md-9">
                                    <textarea v-model="submission.note" class="form-control" id="note" name="note"
                                        placeholder="Ghi chú....">

                                    </textarea>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn event-register">
                            <span v-if="!edit">Đăng ký</span>
                            <span v-if="edit">Cập nhật</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>
<script>

import Treeselect from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
export default {
    components: {
        Treeselect
    },
    props: {
        fetchSubmisstion: Function,
        submission_registers: Array,
    },
    data() {
        return {
            valueConsistsOf: 'BRANCH_PRIORITY',
            edit: false,
            errors: {},
            submission: {
                id: '',
                program_id: null,
                customer_id: [],
                edit_customer_id: null,
                note: '',
                images: [],
                image_dels: [],
            },
            program_admins: [],
            programs: [],
            customers: [
                { id: "ALL", label: "Select All" },
                { id: "BRANCH_PRIORITY", label: "BRANCH_PRIORITY" },
            ],
            token: "",
            page_url_get_program: '/api/program',
            page_url_get_customer: '/api/customer',
            page_url_register: '/api/submission-register',
            page_url_update: '/api/submission-update',
            page_url_delete: '/api/submission-delete',
            page_url_admin_program: '/api/program_admin',
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchProgram();
        this.fetchCustomer();
        this.fetchProgramAdmin();
    },
    methods: {
        handleSelectAll() {
            if (this.valueConsistsOf === "ALL") {
                this.submission.customer_id = this.customerOptions
                    .filter(customer => !customer.isDisabled)
                    .map(customer => customer.id);
            } else {
                this.submission.customer_id = [];
            }
        },

        handleSelectNone() {
            if (this.valueConsistsOf === "BRANCH_PRIORITY") {

                this.submission.customer_id = [];

            }
        },
        mapToTreeselect(data) {
            // Hàm ánh xạ dữ liệu thành dạng treeselect
            return data.map(item => {
                return {
                    id: item.id,
                    label: item.name,


                };
            });
        },
        mapToTreeselectProgramAdmin(data) {
            // Hàm ánh xạ dữ liệu thành dạng treeselect
            return data.map(item => {
                return {
                    id: item.program_id,
                    label: item.program.name,

                };
            });
        },
        isCustomerDisabled(customerId) {
            return this.submission_registers.some(
                submission =>
                    submission.program_id === this.submission.program_id &&
                    submission.customer_id === customerId
            );
        },
        fetchProgram() {
            var page_url = this.page_url_get_program;
            fetch(page_url,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: this.token
                    }
                }
            )
                .then(res => res.json())
                .then(res => {
                    this.programs = this.mapToTreeselect(res.data);
                    // this.$emit('programs', this.programs);
                })
        },
        fetchCustomer() {
            var page_url = this.page_url_get_customer;
            fetch(page_url,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: this.token
                    }
                }
            )
                .then(res => res.json())
                .then(res => {
                    this.customers = res.data;
                    // this.$emit('customers', this.customers);
                })
        },
        fetchProgramAdmin() {
            var page_url = this.page_url_admin_program;
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {
                    this.program_admins = this.mapToTreeselectProgramAdmin(data.data);
                })
                .catch(err => {
                    console.log(err);
                });
        },
        addSubmission() {

            var page_url = this.page_url_register;
            var page_update = this.page_url_update;
            if (this.edit == false) {
                fetch(page_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: this.token

                    },
                    body: JSON.stringify(this.submission)
                }).then(res => res.json())
                    .then(res => {
                        if (res.data.success == 1) {
                            // this.$emit('add-submission', res.data.data);
                            this.$props.fetchSubmisstion();
                            this.showMessage('success', 'Đăng ký thành công');
                            $('#form_register').modal('hide');
                            this.resetSubmission();
                        } else {
                            this.errors = res.data.errors;
                            this.showMessage('error', 'Đăng ký không thành công');
                        }
                    })
            } else {
                //update
                fetch(page_update + "/" + this.submission.id, {
                    method: "PATCH",
                    body: JSON.stringify(this.submission),
                    headers: {
                        "content-type": "application/json",
                        Authorization: this.token
                    }
                })
                    .then(res => res.json())
                    .then(data => {

                        if (data.data.success == 1) {

                            // this.$set(this.goodunits, this.goodunitss.index, { ...this.goodunitss });
                            // táo.showMessage('Thông báo', 'Cập nhật thành công');
                            this.showMessage('success', 'Cập nhật thành công');
                            this.$props.fetchSubmisstion();
                            $('#form_register').modal('hide');
                            this.resetSubmission();
                            // this.reset();
                        } else {
                            this.errors = data.data.errors;
                            this.showMessage('error', 'Cập nhật không thành công');
                        }
                    })
                    .catch(err => console.log(err));
            }

        },
        editSubmission(submission) {
            this.errors = {};
            this.edit = true;
            this.submission.id = submission.id;
            this.submission.edit_customer_id = submission.customer_id;
            this.submission.program_id = submission.program_id;
            this.submission.note = submission.note;
            this.submission.images = submission.images;
            this.submission.image_dels = submission.image_dels;
            $('#form_register').modal('show');
        },
        resetSubmission() {
            this.submission.id = '';
            this.valueConsistsOf = 'BRANCH_PRIORITY';
            this.submission.customer_id = [];
            this.submission.edit_customer_id = null;
            this.submission.program_id = null;
            this.submission.note = '';
            this.submission.images = [];
            this.submission.image_dels = [];
        },
        showMessage(type, title, message) {
            if (!title)
                title = "Information";
            toastr.options = {
                positionClass: "toast-bottom-right",
                toastClass: this.getToastClassByType(type),
            };

            toastr[type](message, title);
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
        deleteSubmission(id) {
            if (confirm('Bạn muốn xoá?')) {
                fetch(`${this.page_url_delete}/${id}`, {
                    method: 'delete',
                    headers: {
                        "content-type": "application/json",
                        Authorization: this.token
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.data.success == 1) {
                            this.showMessage('success', 'Xóa thành công');
                            this.$props.fetchSubmisstion();
                            this.resetSubmission();
                        } else {
                            this.errors = data.data.errors;
                            this.showMessage('error', this.errors.id);
                        }

                    });
            }
        },
        show() {
            this.edit = false;
            this.errors = {};
            this.resetSubmission();
            $('#form_register').modal('show');
        },

    },
    computed: {
        customerOptions() {
            return this.customers.map(customer => {
                const isDisabled = this.isCustomerDisabled(customer.id);
                return {
                    id: customer.id,
                    label: customer.dms_code + ' - ' + customer.name,
                    isDisabled: isDisabled
                };
            });
        }
    }
}
</script>
<style lang="scss" scoped>
.event-register {
    background-color: rgb(0, 40, 81);
    color: white;
}
</style>
