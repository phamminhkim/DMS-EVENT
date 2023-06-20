<template>
    <div>
        <div class="modal" id="join_form_event" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" :class="add_video == true ? 'filter-blur' : ''">
                    <form @submit.prevent="addSubmission">
                        <div class="modal-header">
                            <h5 class="modal-title" v-if="!edit">Upload ảnh tham gia chương trình trưng bày</h5>
                            <h5 class="modal-title" v-if="edit">Cập nhật tham gia chương trình trưng bày</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- <div class="form-group">
                                <label class="font-weight-bold label">Dang sách đăng ký tham gia chương trình</label> 
                                <small class="text-danger">*</small>
                                <treeselect  :disable-branch-nodes="true" v-model="submission.submission_id" :options="treeSubmissions"
                                :show-count="true" :multiple="false" id="submission_id" name="submission_id"
                                placeholder="Chọn chương trình đăng ký...."
                             ></treeselect>

        
                            </div> -->
                            <div class="form-group">
                                <label for="" class="font-weight-bold">Ảnh trưng bày</label>
                                <input type="file" name="images[]" multiple class="form-control" accept="image/*"
                                    @change="handleFileUpload" />
                                <div>
                                    <label>Danh sách ảnh đã chọn:</label>
                                    <div class="center overlay" v-if="add_video">
                                        <i class="fa fa-spinner fa-spin fa-4x fa-fw gray center"></i>
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mt-2 image" v-for="(image, index) in submission.images"
                                            :key="index">
                                            <div class="img-ralative">

                                                <video v-if="isVideo(image.image_path)" class="w-100 video" controls
                                                    height="240">
                                                    <source :src="image.image_path" type="video/mp4">
                                                </video>

                                                <img v-else :src="image.image_path" class="w-100 img-responsive mt-2"
                                                    alt="Ảnh đã chọn">
                                                <button @click="deleteImage(index, image)" class="btn img-remove">
                                                    <i class="fad fa-times-circle icon-remove"
                                                        style="--fa-primary-color: #050505; --fa-secondary-color: #e6e6e6; --fa-secondary-opacity: 0.8;"></i>
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <img src="/submission_images/508760.jpg" > -->
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
    </div>
</template>

<script>
import Treeselect from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';

export default {
    props: {
        fetchSubmisstion: Function,
    },
    components: {
        Treeselect
    },
    data() {
        return {

            edit: false,
            errors: {},
            add_video: false,

            submission: {
                id: '',
                submission_id: null,
                images: [],
                image_dels: [],
            },



            programs: [],
            customers: [],
            treeSubmissions: [],

            page_url_image_upload: '/api/submission-upload-image',
            page_url_update: '/api/submission-update-image',
            page_url_delete: '/api/submission-delete',
            page_url_get_program: '/api/program',
            page_url_get_customer: '/api/customer',

            page_url_get_submission_tree: '/api/submission-tree',
        }
    },
    mounted() {

        $("#join_form_event").on("hidden.bs.modal", () => {
            this.resetSubmission();
        });
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;

        this.fetchProgram();
        this.fetchCustomer();
        this.fetchSubmissionTree();
    },
    methods: {
        fetchSubmissionTree() {
            var page_url = this.page_url_get_submission_tree;
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(res => {
                    this.treeSubmissions = res.data;
                    // this.$emit('submission-tree', res.data);
                })
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
        fetchCustomer() {
            var page_url = this.page_url_get_customer;
            fetch(page_url, { headers: { Authorization: this.token } })
                .then(res => res.json())
                .then(res => {
                    this.customers = this.mapToTreeselect(res.data);
                    this.$emit('customers', this.customers);
                })
        },
        fetchProgram() {

            var page_url = this.page_url_get_program;
            fetch(page_url, { headers: { Authorization: this.token } })
                .then(res => res.json())
                .then(res => {

                    this.programs = this.mapToTreeselect(res.data);
                    this.$emit('programs', this.programs);
                }).catch(err => {

                    console.log(err);
                });
        },
        
        addSubmission() {
            var page_url = this.page_url_image_upload;
            var page_update = this.page_url_update;
            this.add_video = true;
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
                            // this.$props.fetchSubmisstion();
                            this.add_video = false;
                            this.showMessage('success', 'Đăng ký thành công', 'Vui lòng chờ duyệt');
                            $('#join_form_event').modal('hide');
                            this.resetSubmission();
                        } else {
                            this.add_video = false;
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
                        'Content-Type': 'application/json',
                        Authorization: this.token
                    },
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data);
                        if (!data.data.errors) {

                            // this.$set(this.goodunits, this.goodunitss.index, { ...this.goodunitss });
                            // táo.showMessage('Thông báo', 'Cập nhật thành công');
                            this.showMessage('success', 'Cập nhật thành công');
                            this.$props.fetchSubmisstion();
                            $('#join_form_event').modal('hide');
                            this.resetSubmission();
                            this.add_video = false;
                            // this.reset();
                        } else {
                            this.errors = data.data.errors;
                            this.add_video = false;
                        }
                    })
                    .catch(err => console.log(err));
            }

        },
        editSubmission(submission) {
            this.edit = true;
            this.submission.id = submission.id;
            this.submission.submission_id = submission.submission_id;
            this.submission.images = [...submission.submission_images];
            this.errors = {};
            $('#join_form_event').modal('show');
        },
        resetSubmission() {
            this.submission.id = '';

            this.submission.submission_id = null;

            this.submission.images = [];
            this.submission.image_dels = [];
        },
        handleFileUpload(event) {
            const files = event.target.files; // Lấy danh sách tệp đã chọn

            // Lặp qua từng tệp và thêm vào mảng images
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                console.log(file);

                let reader = new FileReader();
                reader.readAsDataURL(file);

                // Thêm tệp vào mảng images
                reader.onload = () => {
                    const imageURL = reader.result; // Đường dẫn base64 của ảnh

                    // Thêm tệp và đường dẫn vào mảng images
                    this.submission.images.push({
                        file: file,
                        name: file.name,
                        image_path: imageURL
                    });
                };
            }
        },
        deleteImage(index, item) {
            this.submission.images.splice(index, 1);

            this.submission.image_dels.push(item);

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
                        this.$props.fetchSubmisstion();
                        this.resetSubmission();
                    });
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
        isVideo(path) {
            const videoExtensions = ['.mp4', '.avi', '.mov', '.mkv'];
            const videoMimeTypes = ['video/mp4'];
            const extension = path.substring(path.lastIndexOf('.')).toLowerCase();
            const mimeType = path.substring(path.indexOf(':') + 1, path.indexOf(';')).toLowerCase();
            console.log(videoExtensions.includes(extension) || videoMimeTypes.includes(mimeType));
            return videoExtensions.includes(extension) || videoMimeTypes.includes(mimeType);
        },
        show() {
            this.edit = false;
            this.add_video = false;
            this.errors = {};
            this.resetSubmission();
            $('#join_form_event').modal('show');
        },

    }
}
</script>

<style lang="scss" scoped>
.event-register {
    background-color: rgb(0, 40, 81);
    color: white;
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

.img-responsive {
    max-width: 100%;
    object-fit: cover;
    height: 100%;
}

.video {
    object-fit: cover;
    height: 100%;
}
.filter-blur{
    filter: blur(5px);
}
</style>
