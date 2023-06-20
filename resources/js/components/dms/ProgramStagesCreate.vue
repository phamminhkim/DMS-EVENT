<template>
    <div>
        <div class="modal fade" id="program_stages_create" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form @submit.prevent="addProgramStage">
                        <div class="modal-header">
                            <h5 v-if="!edit" class="modal-title text-uppercase font-weight-bold">Thêm mới</h5>
                            <h5 v-if="edit" class="modal-title text-uppercase font-weight-bold">Cập nhật</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 text-md-right  mt-2">
                                            <label class="font-weight-bold">Chương trình</label><small class="text-danger">
                                                * </small>
                                        </div>
                                        <div class="col-md-9">
                                            <treeselect v-model="program_stage.program_id" :multiple="false"
                                                :options="programs" placeholder="Chọn chương trình..." id="program_id"
                                                name="program_id"
                                                v-bind:class="hasError('program_id') ? 'is-invalid' : ''" />
                                            <span v-if="hasError('program_id')" class="invalid-feedback" role="alert">
                                                <strong>{{ getError('program_id') }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <div class="col-md-3 text-md-right mt-2">
                                    <label class="font-weight-bold">Đợt đánh giá</label> <small class="text-danger"> *
                                    </small>
                                </div>
                                <div class="col-md-4 mt-1">
                                    <input type="number" class="form-control" v-model="program_stage.stage" id="stage"
                                        name="stage" placeholder="Đợt đánh giá"
                                        v-bind:class="hasError('stage') ? 'is-invalid' : ''">
                                    <span v-if="hasError('stage')" class="invalid-feedback" role="alert">
                                        <strong>{{ getError('stage') }}</strong>
                                    </span>
                                </div>
                                <div class="col-md-4"></div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3 text-md-right mt-2">
                                            <label class="font-weight-bold">Ghi chú</label>
                                        </div>
                                        <div class="col-md-9">
                                            <textarea class="form-control" v-model="program_stage.note" id="note"
                                                name="note" rows="3"></textarea>
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
    props: {
        fetchProgramStages: Function,
    },
    components: {
        Treeselect
    },
    data() {
        return {
            token: '',
            loading: false,
            edit: false,
            errors: {},

            program_stage: {
                id: '',
                program_id: null,
                stage: '',
                note: '',
            },

            programs: [],
            page_url_program: "/api/program",
            page_url_post_program_stage: "/api/program-stages-store",
            page_url_update_program_stage: "/api/program-stages-update",
            page_url_delete_program_stage: "/api/program-stages-delete",
            page_url_patch_program_stage: "/api/program-stages-open",
            page_url_patch_program_stage_close: "/api/program-stages-close",

        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchProgram();
    },
    methods: {
        mapToTreeselect(data) {
            // Hàm ánh xạ dữ liệu thành dạng treeselect
            return data.map(item => {
                return {
                    id: item.id,
                    label: item.name,

                };
            });
        },
        fetchProgram() {
            var page_url = this.page_url_program;
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {

                    this.programs = this.mapToTreeselect(data.data);
                    this.$emit('programs', this.programs);
                })
                .catch(err => {
                    console.log(err);
                });
        },
        addProgramStage() {
            var page_url = this.page_url_post_program_stage;
            var page_url_update = this.page_url_update_program_stage;
            if (this.edit === false) {
                fetch(page_url, {
                    method: "POST",
                    body: JSON.stringify(this.program_stage),
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        //console.log(data.data.success)
                        if (data.data.success == 1) {
                            this.showMessage('success', 'Thêm thành công');
                            this.$props.fetchProgramStages();
                            this.reset();
                            $('#program_stages_create').modal('hide');

                        } else {
                            this.errors = data.data.errors;
                            this.showMessage('error', 'Thêm mới không thành công');

                        }
                    })
                    .catch(err => {
                        this.loading = false;

                    });
            } else {
                //update
                fetch(page_url_update + "/" + this.program_stage.id, {
                    method: "PUT",
                    body: JSON.stringify(this.program_stage),
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {

                        if (data.data.success == 1) {

                            this.showMessage('success', 'Cập nhật thành công');
                            this.$props.fetchProgramStages();
                            $('#program_stages_create').modal('hide');
                            this.reset();
                            //this.clearError();
                        } else {
                            this.errors = data.data.errors;
                            this.showMessage('error', this.errors.id);
                        }
                    })
                    .catch(err => console.log(err));
            }
        },
        editProgramStage(program_stage) {
            this.edit = true;
            this.program_stage.id = program_stage.id;
            this.program_stage.program_id = program_stage.program_id;
            this.program_stage.stage = program_stage.stage;
            this.program_stage.note = program_stage.note;
            $('#program_stages_create').modal('show');
        },
        deleteProgramStage(id) {
            if (confirm('Bạn muốn xoá?')) {
                fetch(`${this.page_url_delete_program_stage}/${id}`, {
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
                            this.$props.fetchProgramStages();
                            this.reset();
                        } else {
                            this.errors = data.data.errors;
                            this.showMessage('error', this.errors.id);
                        }

                    });
            }
        },
        programStageOpen(id) {
            //update
            fetch(this.page_url_patch_program_stage + "/" + id, {
                method: "PATCH",
                headers: {
                    Authorization: this.token,
                    "content-type": "application/json"
                }
            })
                .then(res => res.json())
                .then(data => {

                    if (data.data.success == 1) {
                        this.showMessage('success', 'Mở thành công');
                        this.$props.fetchProgramStages();
                    } else {
                        this.errors = data.data.errors;
                        this.showMessage('error', 'Mở không thành công');
                    }
                })
                .catch(err => console.log(err));
        },
        programStageClose(id) {
            //update
            fetch(this.page_url_patch_program_stage_close + "/" + id, {
                method: "PATCH",
                headers: {
                    Authorization: this.token,
                    "content-type": "application/json"
                }
            })
                .then(res => res.json())
                .then(data => {

                    if (data.data.success == 1) {
                        this.showMessage('success', 'Đóng thành công');
                        this.$props.fetchProgramStages();
                    } else {
                        this.errors = data.data.errors;
                        this.showMessage('error', 'Đóng không thành công');
                    }
                })
                .catch(err => console.log(err));
        },
        reset() {
            this.edit = false;
            this.program_stage.id = '';
            this.program_stage.program_id = null;
            this.program_stage.stage = '';
            this.program_stage.note = '';
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
        show() {
            this.reset();
            this.errors = {};
            $('#program_stages_create').modal('show');
        }
    }
}
</script>