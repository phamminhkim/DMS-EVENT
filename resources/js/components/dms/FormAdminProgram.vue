<template>
    <div>
        <div class="modal fade" id="form_admin_program" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form @submit.prevent="addProgramAdmin">
                        <div class="modal-header">
                            <h5 v-if="!edit" class="modal-title font-weight-bold text-uppercase">Thêm Admin vào chương trình
                            </h5>
                            <h5 v-if="edit" class="modal-title font-weight-bold text-uppercase">Chỉnh sửa Admin chương trình
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <div class="row justify-content-start">
                                    <div class="col-md-4">
                                        <label>Chương trình</label>
                                    </div>
                                    <div class="col-md-8">
                                        <treeselect v-model="program_admin.program_id" :options="programs" :multiple="false"
                                            id="program_id" name="program_id" placeholder="Chọn chương trình đăng ký...."
                                            v-bind:class="hasError('program_id') ? 'is-invalid' : ''">
                                        </treeselect>
                                        <span v-if="hasError('program_id')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('program_id') }}</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row justify-content-start">
                                    <div class="col-md-4">
                                        <label>Admin</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div v-if="!edit">
                                            <p class="options">
                                                <label class="mr-2">
                                                    <input class="mr-2" type="radio" value="ALL" v-model="valueConsistsOf"
                                                        @change="handleSelectAll">Chọn tất cả</label>
                                                <label><input class="mr-2" type="radio" value="BRANCH_PRIORITY" v-model="valueConsistsOf"
                                                        @change="handleSelectNone">None</label>
                                            </p>
                                            <treeselect v-model="program_admin.user_id" :options="users" :multiple="true"
                                                id="user_id" name="user_id" placeholder="Chọn admin...."
                                                :value-consists-of="valueConsistsOf"
                                                v-bind:class="hasError('user_id') ? 'is-invalid' : ''">
                                            </treeselect>
                                            <span v-if="hasError('user_id')" class="invalid-feedback" role="alert">
                                                <strong>{{ getError('user_id') }}</strong>
                                            </span>

                                        </div>
                                        <div v-if="edit">
                                            <treeselect v-model="program_admin.user" :options="users" :multiple="false"
                                                id="user" name="user" placeholder="Chọn admin...."
                                                v-bind:class="hasError('user') ? 'is-invalid' : ''">
                                            </treeselect>
                                            <span v-if="hasError('user')" class="invalid-feedback" role="alert">
                                                <strong>{{ getError('user') }}</strong>
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Treeselect from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
export default {
    components: {
        Treeselect,
    },
    props: {
        fetchProgramAdmin: Function,
    },
    data() {
        return {
            token: '',
            edit: false,
            loading: false,
            errors: {},
            valueConsistsOf: 'BRANCH_PRIORITY',
            program_admin: {
                id: '',
                program_id: null,
                user_id: [],
                user: null,
            },

            programs: [],
            users: [
                { id: "ALL", label: "Select All" },
                { id: "BRANCH_PRIORITY", label: "BRANCH_PRIORITY" },
            ],
            page_url_get_program: '/api/program',
            page_urrl_get_user: '/api/user',
            page_url_store_program_admin: '/api/program_admin_store',
            page_url_update_program_admin: '/api/program_admin_patch',
            page_url_delete_program_admin: '/api/program_admin_delete',
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;

        this.fetchProgram();
        this.fetchUser();
    },

    methods: {
        handleSelectAll() {
            if (this.valueConsistsOf === "ALL") {
                this.program_admin.user_id = this.users.map(user => user.id);
            } else {
                this.program_admin.user_id = [];
            }
        },
        handleSelectNone() {
            if (this.valueConsistsOf === "BRANCH_PRIORITY") {
                console.log(this.valueConsistsOf);
                this.program_admin.user_id = [];
              
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
        mapToTreeselectUser(data) {
            // Hàm ánh xạ dữ liệu thành dạng treeselect
            return data.map(item => {
                return {
                    id: item.id,
                    label: item.staff_code + ' - ' + item.name,
                };
            });
        },
        fetchProgram() {
            var page_url = this.page_url_get_program;
            fetch(page_url, { headers: { Authorization: this.token } })
                .then(res => res.json())
                .then(res => {
                    this.programs = this.mapToTreeselect(res.data);
                    this.$emit('fetchProgramAdmin', this.programs);
                }).catch(err => {
                    console.log(err);
                });
        },
        fetchUser() {
            var page_url = this.page_urrl_get_user;
            fetch(page_url, { headers: { Authorization: this.token } })
                .then(res => res.json())
                .then(res => {
                    this.users = this.mapToTreeselectUser(res.data);
                }).catch(err => {
                    console.log(err);
                });
        },
        addProgramAdmin() {
            var page_url = this.page_url_store_program_admin;
            var page_url_update = this.page_url_update_program_admin;
            if (this.edit == false) {
                fetch(page_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: this.token

                    },
                    body: JSON.stringify(this.program_admin)
                }).then(res => res.json())
                    .then(res => {
                        if (res.data.success == 1) {
                            // this.$emit('add-submission', res.data.data);
                            this.$props.fetchProgramAdmin();
                            this.showMessage('success', 'Đăng ký thành công');
                            $('#form_admin_program').modal('hide');
                            this.reset();
                        } else {
                            this.errors = res.data.errors;
                            this.showMessage('error', 'Đăng ký không thành công');
                        }
                    })
            }
            else {

                fetch(page_url_update + "/" + this.program_admin.id, {
                    method: "PATCH",
                    body: JSON.stringify(this.program_admin),
                    headers: {
                        "content-type": "application/json",
                        Authorization: this.token
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.data.errors) {
                            this.showMessage('success', 'Cập nhật thành công');
                            this.$props.fetchProgramAdmin();
                            $('#form_admin_program').modal('hide');
                            this.reset();
                        } else {
                            this.errors = data.data.errors;
                        }
                    })
                    .catch(err => console.log(err));
            }
        },
        editProgramAdmin(program_admin) {
            this.errors = {};
            this.edit = true;
            this.program_admin.id = program_admin.id;
            this.program_admin.program_id = program_admin.program_id;
            this.program_admin.user = program_admin.user_id;
            $('#form_admin_program').modal('show');
        },
        deleteProgramAdmin(id) {
            if (confirm('Bạn muốn xoá?')) {
                fetch(`${this.page_url_delete_program_admin}/${id}`, {
                    method: 'delete',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: this.token
                    },

                })
                    .then(res => res.json())
                    .then(data => {
                        this.showMessage('success', 'Xóa thành công');
                        this.$props.fetchProgramAdmin();
                        this.reset();
                    });
            }
        },
        reset() {
            this.edit = false;
            this.valueConsistsOf= 'BRANCH_PRIORITY';
            this.program_admin = {
                id: '',
                program_id: null,
                user_id: [],
                user: null,
            };
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
        showModal() {
            this.edit = false;
            this.reset();
            this.errors = {};
            $('#form_admin_program').modal('show');
        }
    }
}
</script>