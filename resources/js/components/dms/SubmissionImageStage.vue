<template>
    <div class="modal fade" id="submission_stage_image" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form @submit.prevent="addSubmission">
                    <div class="modal-header">
                        <h5 v-if="!edit" class="modal-title text-uppercase font-weight-bold">Upload Ảnh</h5>
                        <h5 v-if="edit" class="modal-title text-uppercase font-weight-bold">Cập nhật</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-goup form-search">
                            <label class="font-weight-bold">Chương trình khách hàng tham gia </label> <small
                                class="text-danger"> * </small>
                            <input class="form-control input-search" type="text" id="submission_id" name="submission_id"
                                placeholder="Nhập chương trình khách tham gia....." autocomplete="off"
                                v-model="searchKeyword" @keyup.down="navigateDown()" @keyup.up="navigateUp()"
                                @keyup.enter="selectItem(submissions[selectedIndex])"
                                v-bind:class="hasError('submission_id') ? 'is-invalid' : ''">
                                <span v-if="hasError('submission_id')" class="invalid-feedback" role="alert">
                                    <strong>{{ getError('submission_id') }}</strong>
                                </span>
                            <div class="card item-search" style="height:500px;overflow-y:scroll">
                                <div class="text-center" v-if="loading">
                                    <i class="fad fa-spinner fa-pulse "
                                        style="--fa-primary-color: #3277d2; --fa-primary-opacity: 0.3; --fa-secondary-color: #2a3ea2; --fa-secondary-opacity: 0.8;font-size:30px"></i>
                                    <br>
                                   
                                </div>
                                <span class="mt-1 p-2 font-italic text-secondary">Tìm thấy {{ submissions.length }}/{{
                                    paginate_submission_stage_image.total }} kết quả</span>
                                <div class="form-group query" v-for="(query, index) in submissions" :key="index"
                                    @click="getValue(query)" :class="{ 'selected': index === selectedIndex }">
                                    <div class="mt-1">
                                        <span class="text-secondary">
                                            <span class="font-weight-bold">{{ index + 1 }} </span> .
                                            <span v-html="highlightMatched(query.customer.name)"></span>
                                            <span v-html="highlightMatched(query.program.name)" class="float-right"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <label class="font-weight-bold">Chọn đợt đánh giá </label><small class="text-danger"> * </small>
                            <select class="form-control" v-model="submisstion_image.program_stage_id" id="program_stage_id" name="program_stage_id"
                            v-bind:class="hasError('program_stage_id') ? 'is-invalid' : ''">
                                <option v-for="(program_stage, index) in program_stages" :key="index"
                                    :value="program_stage.id">
                                   <span class="font-weight-bold">{{ program_stage.program.name }}</span>
                                   <span class="float-right">Đợt: {{ program_stage.stage }}</span>
                                </option>
                            </select>
                            <span v-if="hasError('program_stage_id')" class="invalid-feedback" role="alert">
                                <strong>{{ getError('program_stage_id') }}</strong>
                            </span>
                        </div>
                        <div class="form-group mt-2">
                            <div class="form-group">
                                <label class="font-weight-bold">Ảnh trưng bày</label>
                                <input type="file" name="images[]" multiple class="form-control" accept="image/*"
                                    @change="handleFileUpload" />
                                <div>
                                    <label>Danh sách ảnh đã chọn:</label>
                                    <div class="row">
                                        <div class="col-md-4 mt-2 image" v-for="(image, index) in submisstion_image.images"
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        fetchSubmissionStageImage: Function,
    },
    data() {
        return {
            token: '',
            loading: false,
            edit: false,
            selectedIndex: -1,
            typingTimer: null,
            searchKeyword: '',
            errors:{},
            submisstion_image: {
                id: '',
                submission_id: '',
                program_stage_id: '',
                images: [],
                image_dels: [],
            },
            paginate_submission_stage_image: {
                current_page: 1,
                last_page: 1,
                total: 0,
            },
            perPage_submission: 30,

            submissions: [],
            program_stages: [],
            page_url_program_stages: "/api/program-stages-open",
            page_url_submission: '/api/submission-register-search',
            page_url_image_upload: '/api/submission-upload-image',
            page_url_image_update: '/api/submission-update-image',
            page_url_image_delete: '/api/submission-delete-image',
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchSubmisstion();
        this.fetchProgramStage();

    },
    watch: {
        searchKeyword: function (newVal, oldVal) {
            clearTimeout(this.typingTimer);
            this.typingTimer = setTimeout(() => {
                this.fetchSubmisstion();
                if(this.searchKeyword == ''){
                    this.submisstion_image.submission_id = '';
                }
            }, 500);

        }
    },
    methods: {
        fetchProgramStage() {
            var page_url = this.page_url_program_stages;
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {

                    this.program_stages = data.data;
                })
                .catch(err => {
                    console.log(err);
                });
        },
        fetchSubmisstion() {
            this.loading = true;
            this.errors = {};
            const queryParams = new URLSearchParams({
                query: this.searchKeyword,
            });
            var page_url = this.page_url_submission + "/" + this.paginate_submission_stage_image.current_page + "?per_page=" + this.perPage_submission + '&' + queryParams.toString();
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {
                    this.submissions = data.data;
                    this.paginate_submission_stage_image = data.paginate;
                    this.selectedIndex = -1;
                    this.loading = false;
                })
                .catch(err => {
                    console.log(err);
                    this.loading = false;

                });
        },
        addSubmission() {
            var page_url = this.page_url_image_upload;
            var page_update = this.page_url_image_update;
            // this.add_video = true;
            if (this.edit == false) {
                fetch(page_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: this.token
                    },
                    body: JSON.stringify(this.submisstion_image)
                }).then(res => res.json())
                    .then(res => {

                        if (res.data.success == 1) {

                            this.showMessage('success', 'Đăng ký thành công', 'Vui lòng chờ duyệt');
                            $('#submission_stage_image').modal('hide');
                            this.$props.fetchSubmissionStageImage();
                            this.reset();
                        } else {

                            this.errors = res.data.errors;
                            this.showMessage('error', 'Đăng ký không thành công');
                        }
                    })
            } else {
                //update

                fetch(page_update + "/" + this.submisstion_image.id, {
                    method: "PATCH",
                    body: JSON.stringify(this.submisstion_image),
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: this.token
                    },
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);
                        if (data.data.success == 1) {
                            this.showMessage('success', 'Cập nhật thành công');
                            this.$props.fetchSubmissionStageImage();
                            $('#submission_stage_image').modal('hide');
                            this.reset();
                        } else {
                            this.errors = data.data.errors;
                            this.showMessage('error', 'Cập nhật không thành công');
                        }
                    })
                    .catch(err => console.log(err));
            }

        },
        editSubmissionStageImage(submission_stage_image) {
            this.edit = true;
            this.submisstion_image.id = submission_stage_image.id;
            this.submisstion_image.submission_id = submission_stage_image.submission_id;
            this.searchKeyword = submission_stage_image.submission.customer.name + ' - ' + submission_stage_image.submission.program.name;
            this.submisstion_image.program_stage_id = submission_stage_image.program_stage_id;
            this.submisstion_image.images = [...submission_stage_image.files];
            this.errors = {};
            $('#submission_stage_image').modal('show');
        },
        deleteSubmissionImage(id) {
            if (confirm('Bạn muốn xoá?')) {
                fetch(`${this.page_url_image_delete}/${id}`, {
                    method: 'delete',
                    headers: {
                        "content-type": "application/json",
                        Authorization: this.token
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if(data.data.success == 1){
                            this.showMessage('success', 'Xoá thành công');
                            this.$props.fetchSubmissionStageImage();
                        }
                     
                    });
            }
        },
        handleFileUpload(event) {
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
                    this.submisstion_image.images.push({
                        file: file,
                        name: file.name,
                        url: imageURL
                    });
                };
            }
        },
        deleteImage(index, item) {
            this.submisstion_image.images.splice(index, 1);
            this.submisstion_image.image_dels.push(item);
        },
        getValue(name) {
            this.submisstion_image.submission_id = name.id;
            this.searchKeyword = name.customer.name;
        },
        navigateDown() {
            if (this.selectedIndex < this.submissions.length - 1) {
                this.selectedIndex++;
            }
        },
        navigateUp() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
            }
        },
        selectItem(query) {
            this.getValue(query);
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
        showMessage(type, title, message) {
            if (!title)
                title = "Information";
            toastr.options = {
                positionClass: "toast-top-right",
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
        showModal() {
            this.edit = false;
            this.errors = {};
            this.reset();

            $('#submission_stage_image').modal('show');
        },
        reset(){
            this.submisstion_image = {
                id: '',
                submission_id: '',
                program_stage_id: '',
                images: [],
                image_dels: [],
            };
            this.errors = {};
            this.edit = false;
            this.searchKeyword = '';
        },
        highlightMatched(str) {
            const regex = new RegExp(this.searchKeyword, 'gi');
            return str.replace(regex, match => `<span class="text-black font-weight-bold">${match}</span>`)
        },
    }
}
</script>

<style lang="scss" scoped>
.form-search {
    position: relative;
}

.item-search {
    position: absolute;
    width: 100%;
    z-index: 2;
    visibility: hidden;
    opacity: 0;
    transition: opacity .3s ease-out, visibility .3s ease-out;

    &::before {
        position: absolute;
        content: '';
        width: 100%;
        background-color: red;
    }
}


.query {
    padding: 5px;

    &:hover {
        border-left: 3px solid darkblue;
        background: rgb(242, 242, 242);
        cursor: pointer;
    }

}


input:focus+.item-search {
    visibility: visible;
    opacity: 1;
}

.search-item {
    color: gray;
    font-weight: 700;

    &:hover {
        border: 1px solid gray;
        color: rgb(0, 140, 255);
    }

}

.position-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.selected {
    background-color: #e4e4e4;
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

}</style>