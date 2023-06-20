<template>
    <div>
        <!-- container -->
        <div class="">
            <div class="form-group row">
                <div class="col-md-12">
                    <div class="row  shadow-sm p-3 ">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6">
                            <input type="search" class="form-control" v-model="filter" placeholder="Tìm kiếm...."
                                aria-label="Search" style="border-radius:30px;padding-left: 35px">
                            <i class="fas fa-search fa-rotate-90 text-secondary"
                                style="position: absolute;top: 12px;left: 27px;"></i>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                </div>

            </div>
            <div class="text-right form-group mr-3">
                <button @click="showModal()" class="btn btn-sm btn-info" style="height: 35px;width: 90px;">
                    + Tạo mới
                </button>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="text-center">
                        <h4 class="text-uppercase font-weight-bold">Danh sách chương trình </h4>
                    </div>
                </div>
            </div>
            <div class="container-header">
                <div>
                    <b-table responsive hover small striped :bordered="true" :sticky-header="true"
                        :current-page="current_page" :per-page="per_page" :filter="filter" :fields="fields"
                        :items="programs" :tbody-tr-class="rowClass">
                        <template #cell(index)="data">
                            {{ data.index + (current_page - 1) * per_page + 1 }}
                        </template>
                        <template #cell(file)="data">
                            <div v-if="data.item.files.length > 0">
                                <div class="form-group mb-0" v-for="(file,index_file) in data.item.files" >
                                    <span> {{ index_file + 1 }}. </span>
                                    <span> {{ file.name }} </span>
                                    <span @click.prevent="downloadFile(file.id, file.name)" class="float-right" style="cursor:pointer"><i
                                        class="fas fa-download"></i></span>
                                </div>
                            </div>
                          
                        </template>
                       
                        <template #cell(action)="data">
                            <div class="margin">
                                <button class="btn btn-xs" style="margin-right: 10px;" @click="editProgram(data.item)"><i
                                        class="fas fa-edit text-green" style="color: green;" title="Edit"></i></button>

                                <button class="btn btn-xs" @click="deleteProgram(data.item.id)"><i
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
                                <b-form-select size="sm" v-model="per_page" :options="pageOptions">
                                </b-form-select>
                            </div>
                            <label class="col-form-label-sm col-md-1" style="text-align: left" for=""></label>
                            <div class="col-md-3">
                                <b-pagination v-model="current_page" :total-rows="rows" :per-page="per_page" size="sm"
                                    class="ml-1"></b-pagination>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end phân trang -->
                <!-- tạo form -->
                <div class="modal fade" id="dms_program" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <form @submit.prevent=addProgram>
                                <div class="modal-header">
                                    <h4 class="modal-title">
                                        <span v-if="!edit">Thêm mới chương trình</span>
                                        <span v-if="edit">Cập nhật chương trình</span>
                                    </h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>(DMS Code)</label>
                                        <input v-model="program.dms_code" class="form-control" id="dms_code" name="dms_code"
                                            placeholder="Nhập mã chương trình (DMS) ..."
                                            v-bind:class="hasError('dms_code') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('dms_code')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('dms_code') }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label>Tên chương trình</label>
                                        <small class="text-danger">*</small>
                                        <input v-model="program.name" class="form-control" id="name" name="name"
                                            placeholder="Nhập tên chương trình ..."
                                            v-bind:class="hasError('name') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('name')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('name') }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label>Ngày bắt đầu</label>
                                        <small class="text-danger">*</small>
                                        <input v-model="program.start_date" type="date" class="form-control" id="start_date"
                                            name="start_date" placeholder="Nhập ngày bắt đầu ..."
                                            v-bind:class="hasError('start_date') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('start_date')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('start_date') }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label>Ngày kết thúc</label>
                                        <small class="text-danger">*</small>
                                        <input v-model="program.end_date" type="date" class="form-control" id="end_date"
                                            name="end_date" placeholder="Nhập ngày kết thúc ..."
                                            v-bind:class="hasError('end_date') ? 'is-invalid' : ''" />
                                        <span v-if="hasError('end_date')" class="invalid-feedback" role="alert">
                                            <strong>{{ getError('end_date') }}</strong>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <label>Ghi chú</label>
                                        <textarea v-model="program.note" class="form-control" id="note" name="note"
                                            placeholder="Nhập ghi chú ..."></textarea>
                                    </div>
                                    <div class="form-group">
                                        <input type="file" class="form-control"
                                            accept=".xlsx,.xls,.doc, .docx,.ppt, .pptx,.txt,.pdf"
                                            multiple @change="handleFileUpload" ref="fileInput" style="display:none" />
                                        <button type="button" class="btn right btn-sm cssfile"
                                            style="font-weight: 600; height:30px; border:1px solid lightgray"
                                            @click="openFileInput">Thêm File đính kèm</button>

                                    </div>
                                    <div class="form-group" v-for="(file, index) in program.files" v-bind:key="index">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div style="font-size:12px;display:inline-block;width:78%">
                                                    <span><i> {{ index + 1 + '.' + file.name }}</i></span>
                                                </div>
                                                <div class="float-right">
                                                    <button style="border-radius:50px;font-size:12px" class="btn text-red"
                                                        @click.prevent="deleteFile(index, file)"><i
                                                            class="fas fa-trash fa-lg text-danger"></i></button>
                                                    <button type="button" class="btn btn-default btn-xs "
                                                        style="background:darkcyan;color:white;border-radius:10px;"
                                                        @click.prevent="downloadFile(file.id, file.name)"><i
                                                            class="fas fa-download"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Ảnh chương trình</label>
                                        <input type="file" name="images[]" multiple class="form-control" accept="image/*"
                                            @change="handleFileUploadImage" />
                                        <div>
                                            <label>Danh sách ảnh đã chọn:</label>
                                            <div class="row">
                                                <div class="col-md-6 mt-2 image" v-for="(image, index) in program.images"
                                                    :key="index">
                                                    <div class="img-ralative">
                                                        <img :src="image.url" class="w-100 img-responsive mt-2" alt="Ảnh đã chọn" loading="lazy">
                                                        <button @click.prevent="deleteImage(index, image)" class="btn img-remove">
                                                            <i class="fad fa-times-circle icon-remove"
                                                                style="--fa-primary-color: #050505; --fa-secondary-color: #e6e6e6; --fa-secondary-opacity: 0.8;"></i>
                                                        </button>
                                                    </div>
        
                                                </div>
                                            </div>
                                        </div>
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
                <!-- end tạo form -->
            </div>
        </div>
        <!-- end container -->
    </div>
</template>

<script>

import ViewSubmission from './ViewSubmission.vue';
export default {
    components: {
        ViewSubmission

    },

    data() {
        return {

            token: '',
            pagesNumber: [],
            placeholderText: "Tìm kiếm ",
            loading: false,
            show_search: false,
            edit: false,
            errors: {},
            program: {
                id: '',
                dms_code: '',
                name: '',
                start_date: '',
                end_date: '',
                note: '',
                files: [],
                file_dels: [],
                images: [],
                image_dels: [],

            },
            //component per-page
            pagination: {
                total: 0,
                per_page: 2,
                from: 1,
                to: 0,
                current_page: 1,
            },
            per_page: 10,
            current_page: 1,
            pageOptions: [10, 50, 100, 500, { value: this.rows, text: "All" }],

            //search
            filter: "",

            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    sortable: true,
                    class: 'text-nowrap',
                },

                {
                    key: 'dms_code',
                    label: 'Mã chương trình',
                    sortable: true,
                    class: 'text-nowrap',

                },

                {
                    key: 'name',
                    label: 'Tên chương trình',
                    sortable: true,
                    class: 'text-nowrap',

                },

                {
                    key: 'start_date',
                    label: 'Ngày bắt đầu',
                    sortable: true,
                    class: 'text-nowrap',

                },

                {
                    key: 'end_date',
                    label: 'Ngày kết thúc',
                    sortable: true,
                    class: 'text-nowrap',

                },
                {
                    key: 'note',
                    label: 'Ghi chú',
                    sortable: true,
                    class: 'text-nowrap',

                },
                {
                    key: 'file',
                    label: 'File đính kèm',
                    sortable: true,
                    class: 'text-nowrap',

                },
              
                {
                    key: 'action',
                    label: 'Tùy chỉnh',
                    sortable: true,
                    class: 'text-nowrap',

                }
            ],

            programs: [],
            page_url_program: "/api/program",
            page_url_create_program: '/api/program_store',
            page_url_update_program: '/api/program_put',
            page_url_destroy_program: '/api/program_delete',
            page_url_download_file: '/api/download_file',


        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchProgram();
    },
    methods: {
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

                    this.programs = data.data;
                })
                .catch(err => {
                    console.log(err);
                });
        },
        addProgram() {
            var page_url = this.page_url_create_program;
            var page_url_update = this.page_url_update_program;
            if (this.edit === false) {
                fetch(page_url, {
                    method: "POST",
                    body: JSON.stringify(this.program),
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
                            this.fetchProgram();
                            $('#dms_program').modal('hide');
                            this.reset();

                        } else {
                            this.errors = data.data.errors;
                            this.showMessage('error', 'Thêm mới không thành công');
                            this.fetchProgram();
                            //this.reset();


                        }
                    })
                    .catch(err => {
                        this.loading = false;

                    });
            } else {
                //update
                fetch(page_url_update + "/" + this.program.id, {
                    method: "PUT",
                    body: JSON.stringify(this.program),
                    headers: {
                        Authorization: this.token,
                        "content-type": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {

                        if (data.data.success == 1) {

                            this.showMessage('success', 'Cập nhật thành công');
                            this.fetchProgram();
                            $('#dms_program').modal('hide');
                            this.reset();
                            //this.clearError();
                        } else {
                            this.errors = data.data.errors;
                            this.showMessage('error', 'Cập nhật không thành công');
                            this.fetchProgram();
                            //this.reset();


                        }
                    })
                    .catch(err => console.log(err));
            }

        },
        deleteProgram(id) {
            if (confirm('Bạn muốn xoá?')) {
                fetch(`${this.page_url_destroy_program}/${id}`, {
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
                            this.fetchProgram();
                            this.reset();
                        } else {
                            this.errors = data.data.errors;
                            this.showMessage('error', this.errors.id);
                        }
                    });
            }
        },
        editProgram(item) {
            this.edit = true;
            this.errors = {};
            this.program.id = item.id;
            this.program.dms_code = item.dms_code;
            this.program.name = item.name;
            this.program.start_date = item.start_date;
            this.program.end_date = item.end_date;
            this.program.note = item.note;
            this.program.files = item.files;
            this.program.file_dels = [];
            this.program.images = item.images;
            this.program.image_dels = [];
            $('#dms_program').modal('show');
            //this.clearError();

        },
        deleteFile(index, item) {
            this.program.files.splice(index, 1);
            this.program.file_dels.push(item);
        },
        downloadFile(idfile, filename) {
            var page_url = this.page_url_download_file + "/" + idfile;
            fetch(page_url, {
                headers: {
                    Authorization: this.token,
                    "content-type": "application/json"
                },
                responseType: 'arraybuffer',
                method: "post"
            })
                .then(res => res.blob())
                .then(res => {
                    console.log(res);
                    var newBlob = new Blob([res], { type: "octet/stream" });
                    if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                        window.navigator.msSaveOrOpenBlob(newBlob);
                        return;
                    }
                    // For other browsers:
                    // Create a link pointing to the ObjectURL containing the blob.
                    const data = URL.createObjectURL(newBlob);
                    var link = document.createElement('a');
                    link.href = data;
                    link.download = filename;
                    link.click();

                    setTimeout(function () {
                        // For Firefox it is necessary to delay revoking the ObjectURL
                        URL.revokeObjectURL(data)
                    }, 100);
                }).catch(err => console.log(err));

        },
        createImage(file) {
            let reader = new FileReader();
            let vm = this;
            reader.onload = (e) => {
                vm.image = e.target.result;
            };
            reader.readAsDataURL(file);
            console.log(file);
            //file.target.value = "";
        },
        onImageChange(e) {
            let files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.createImage(files);
        },
        handleFileUpload(event) {
            const uploadedFiles = Array.from(event.target.files);

            uploadedFiles.forEach(file => {
                const reader = new FileReader();
                reader.readAsDataURL(file);

                reader.onload = () => {
                    const uploadedFile = {
                        name: file.name,
                        size: file.size,
                        ext: file.type.split("/").pop(),
                        lastModifiedDate: file.lastModifiedDate,
                        base64: reader.result
                    };

                    // Kiểm tra xem file đã tồn tại trong mảng this.program.files chưa
                    const existingFile = this.program.files.find(f => f.name === uploadedFile.name);
                    if (!existingFile) {
                        this.program.files.push({ ...uploadedFile });
                    }
                };
            });

            event.target.value = ""; // Xóa giá trị của input file
        },
        openFileInput() {
            this.$refs.fileInput.click();
        },
        handleFileUploadImage(event) {
            const files = event.target.files; // Lấy danh sách tệp đã chọn
            // Lặp qua từng tệp và thêm vào mảng images
            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                let reader = new FileReader();
                reader.readAsDataURL(file);

                // Thêm tệp vào mảng images
                reader.onload = () => {
                    const imageURL = reader.result; // Đường dẫn base64 của ảnh

                    // Thêm tệp và đường dẫn vào mảng images
                    this.program.images.push({
                        file: file,
                        name: file.name,
                        url: imageURL
                    });
                };
            }
        },
        deleteImage(index, item) {
            this.program.images.splice(index, 1);
            this.program.image_dels.push(item);
        },
        reset() {
            this.program.id = '';
            this.program.dms_code = null,
            this.program.name = '';
            this.program.start_date = '';
            this.program.end_date = '';
            this.program.note = '';
            this.program.files = [];
            this.program.file_dels = [];
            this.program.images = [];
            this.program.image_dels = [];

        },
        showModal() {
            this.edit = false;
            //console.log('thu');
            this.errors = {};
            $('#dms_program').modal('show');
            this.reset();
        },
        placeholder() {
            return this.placeholderText;
        },
        rowClass(item, type) {
            if (!item || type !== 'row') return
            if (item.status === 'awesome') return 'table-success'
        },
        showViewProgram(program) {
            this.$refs.viewSubmission.viewSubmission(program);
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
        showSearch() {
            this.show_search = !this.show_search;
        },

    },
    computed: {
        rows() {
            return this.programs.length;
        },
    }
}

</script>

<style lang="scss" scoped
>
.table {

    margin-bottom: 0px;
}
.img-ralative {
    position: relative;
    width: 100%;
    height: 100%;

}
.img-remove {
    position: absolute;
    right: 0;
    z-index: 1;
    color: white;

}
</style>