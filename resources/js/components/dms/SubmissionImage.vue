<template>
    <div>
        <div class="container-fluid" style="padding-left: 0;">
            <div class="row">
                <div class="col-lg-2" v-if="current_user.roles !== 'QLGS'" >
                    <button class="btn p-2 btn-sm w-100 d-block d-lg-none d-md-block join-program" @click="showJoinProgram()" type="button">
                        <i class="fas fa-user-check mr-2"></i> Chương trình tham gia
                    </button>
                    <div class="bg-white shadow d-none d-lg-block d-md-none demo"
                        id="myDiv">
                        <div class="nav-left">
                            <div class="mt-2 p-2">
                                <button class="btn p-2 btn-sm w-100 join-program" @click="showJoinProgram()" type="button">
                                    <i class="fas fa-user-check mr-2"></i> Chương trình tham gia
                                </button>
                                <!-- <button class="btn btn-warning btn-sm mt-2 w-100">
                                    Danh sách GSBH
                                </button>
                                <button class="btn btn-warning btn-sm mt-2 w-100">
                                    Danh sách nhân viên
                                </button> -->
                            </div>

                        </div>

                    </div>
                </div>
                <div :class="current_user.roles !== 'QLGS' ? 'col-lg-10' : 'col-lg-12'" style="padding-right:0">
                    <div class="border-bottom shadow-sm p-2 sticky-search">
                        <div class="d-flex justify-content-between" style="padding-right: 15px;">
                            <button @click="showSearch()" class="btn btn-outline  border p-2 px-4 rounded-pill btn-sm mb-1"
                                style="font-weight:600;" type="button" data-toggle="collapse" data-target="#collapseExample"
                                aria-expanded="false" aria-controls="collapseExample">
                                Tìm kiếm chi tiết
                                <span v-if="!show_search"><i class="fas fa-angle-down"></i></span>
                                <span v-if="show_search"><i class="fas fa-angle-up"></i></span>
                            </button>
                            <button type="button"
                                :class="'btn btn-sm mb-1 px-4 custom-reset dropdown-toggle ' + backgroud_color"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span><i class="far fa-star mr-2"></i> {{ status_name }} </span>
                            </button>
                            <div class="dropdown-menu">
                                <a v-for="stt in status" class="dropdown-item" @click.prevent="getValuestatus(stt)"
                                    style="cursor:pointer">
                                    {{ stt.name }}
                                </a>
                            </div>
                        </div>
                        <div class="form-group" id="collapseExample" v-if="show_search">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-1 mt-2 ml-md-5">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>Chương trình</label>
                                                <treeselect class="rounded" v-model="filter.program_id" :options="programs"
                                                    @input="fetchSubmisstionStageImage()" :multiple="false" id="program_id"
                                                    name="program_id" placeholder="Chọn chương trình...."></treeselect>
                                                <div class="mt-2 text-right">
                                                    <input v-model="filter.stage" class="form-control" type="number"
                                                        placeholder="Nhập đợt..." />

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ml-md-5 mb-0">
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <label>Khách hàng</label>
                                                <SearchCustomer ref="searchCustomer"
                                                    @SearchCustomer="fetchSubmisstionStageImage">
                                                </SearchCustomer>
                                                <!-- <treeselect v-model="filter.customer_id" :options="customers" :multiple="false"
                                                        @input="fetchSubmisstionStageImage()" id="customer_id" name="customer_id"
                                                        placeholder="Chọn khách hàng...."></treeselect> -->
                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <div class="col-md-6">
                                    <div class="mb-1 mt-2 ml-md-5">
                                        <label>Ngày gửi duyệt</label>
                                        <div class="d-flex">
                                            <b-form-datepicker v-model="filter.start_date" class="mr-3"
                                                placeholder="Từ ngày.." :max="filter.end_date"></b-form-datepicker>
                                            <span class="mt-2 text-secondary"> đến </span>
                                            <b-form-datepicker v-model="filter.end_date" class="ml-3"
                                                placeholder="Đến ngày.." :min="filter.start_date"></b-form-datepicker>
                                            <!-- <input v-model="filter.start_date" class="form-control mr-3" type="date" placeholder="Từ ngày.." />
                                            <p class="mt-2 text-secondary"> đến </p>
                                            <input v-model="filter.end_date" class="form-control ml-3" type="date" placeholder="Đến ngày.." /> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ml-md-5 mb-0 text-right">
                                <button @click.prevent="searchFilter()" class="btn btn-warning btn-sm mr-3">
                                    <i class="fas fa-search fa-rotate-90 mr-2"></i>Tìm kiếm
                                </button>
                                <button @click.prevent="resetFilter()" class="btn custom-reset"><i
                                        class="fas fa-spinner"></i></button>

                            </div>
                        </div>
                    </div>
                    <div class="bg-white custom-container" ref="content">
                        <div :class="loading == true ? 'myDiv' : ''"></div>

                        <div v-if="is_staff == true" class="text-right mr-2 btn-upload-img">
                            <button @click="showStageImage()"
                                class="btn btn-sm p-2 px-4 text-white font-weight-bold text-uppercase">
                                <i class="fas fa-file-upload mr-2"></i>Upload Ảnh Khách Hàng</button>
                        </div>
                        <div class="container-body">
                            <h4 class="font-weight-bold text-uppercase text-center text-showroom mt-4 mb-2">showroom
                            </h4>
                            <div>
                                <div class="form-group p-4 submisstion-background">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3 mt-1"
                                            v-for="(submission_image, index) in submission_images" :key="index">
                                            <div class="card border-0 submission"
                                                style="background:transparent;height:100%">
                                                <div class="card-header shadow border-0 mb-4 submission-header"
                                                    style="position:relative;">
                                                    <div class="row">
                                                        <div class="col-md-12 ">
                                                            <div class="d-flex">
                                                                <div class="">
                                                                    <b-avatar size="30px" badge-variant="info">
                                                                        <template #badge><i
                                                                                class="fas fa-star"></i></template>
                                                                    </b-avatar>
                                                                </div>
                                                                <div class="ml-2 flex-grow-1">

                                                                    <label
                                                                        class="text-header mb-0 font-weight-bold text-left ">
                                                                        <span v-if="submission_image.submission.program_id">
                                                                            {{ submission_image.submission.program.name }}
                                                                        </span>
                                                                    </label>
                                                                    <div class="form-group mb-0"
                                                                        v-if="submission_image.program_stage_id">
                                                                        <p class="text-opacity mb-0 d-flex justify-content-between"
                                                                            style="font-size: 13px; line-height: 1.5rem;">
                                                                            <span>
                                                                                <i class="far fa-bookmark"
                                                                                    style="width:13px"></i>
                                                                                Đợt: {{ submission_image.program_stage.stage
                                                                                }}
                                                                            </span>

                                                                            <span
                                                                                v-if="submission_image.is_approved_level2 == 1 && submission_image.is_approved == 1"
                                                                                class="badge text-uppercase badge-success  font-weight-bold p-2">
                                                                                Đạt
                                                                            </span>
                                                                            <span
                                                                                v-if="submission_image.is_rejected_level2 == 1 && submission_image.is_approved == 1"
                                                                                class="badge text-uppercase badge-danger font-weight-bold p-2">
                                                                                Từ chối
                                                                            </span>
                                                                            <span
                                                                                v-if="submission_image.is_approved_level2 == 0 && submission_image.is_rejected_level2 == 0 && submission_image.is_rejected == 1"
                                                                                class="badge text-uppercase badge-danger font-weight-bold p-2">
                                                                                Từ chối
                                                                            </span>
                                                                            <span
                                                                                v-if="submission_image.is_approved_level2 == 0 && submission_image.is_rejected_level2 == 0 && submission_image.is_approved == 1"
                                                                                class="badge text-uppercase badge-warning text-white font-weight-bold p-2">
                                                                                Chờ xét duyệt
                                                                            </span>
                                                                            <span
                                                                                v-if="submission_image.is_approved_level2 == 0 && submission_image.is_rejected_level2 == 0 && submission_image.is_approved == 0 && submission_image.is_rejected == 0 && submission_image.send_date !== null"
                                                                                class="badge text-uppercase badge-primary font-weight-bold p-2">
                                                                                Đã gửi duyệt
                                                                            </span>
                                                                            <span v-if="submission_image.send_date == null"
                                                                                class="badge text-uppercase badge-secondary font-weight-bold p-2">
                                                                                Chưa gửi duyệt
                                                                            </span>
                                                                        </p>
                                                                    </div>
                                                                    <p class="text-opacity mb-0 " style="font-size: 13px;">
                                                                        <span v-if="submission_image.submission.customer_id"
                                                                            class="text-sm text-font"> KH:
                                                                            <span
                                                                                v-if="submission_image.submission.customer.dms_code">
                                                                                {{
                                                                                    submission_image.submission.customer.dms_code
                                                                                }} -
                                                                            </span>
                                                                            {{ submission_image.submission.customer.name
                                                                            }}</span>

                                                                    </p>
                                                                    <p class="text-opacity mb-0" style="font-size: 13px">

                                                                        <span v-if="submission_image.user_id"
                                                                            class="text-sm text-font">
                                                                            NVBH: {{ submission_image.user.staff_code }} -
                                                                            {{
                                                                                submission_image.user.name }}
                                                                        </span>
                                                                    </p>

                                                                    <div v-if="submission_image.feedback_content"
                                                                        class="form-group mb-0">
                                                                        <p class="text-opacity font-weight-bold mb-0 text-font"
                                                                            style="font-size: 13px;">
                                                                            Ghi chú: {{
                                                                                submission_image.feedback_content
                                                                            }}
                                                                        </p>
                                                                    </div>
                                                                    <div v-if="submission_image.send_date"
                                                                        class="form-group mb-0">
                                                                        <p class="text-sm text-success mb-0 text-font"
                                                                            style="font-size: 13px;">
                                                                            Gửi duyệt: {{
                                                                                submission_image.send_date
                                                                                | formatDate }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between mt-2 mb-2">
                                                        <div v-if="is_staff == true" class="text-right">
                                                            <div v-if="submission_image.is_rejected == 0 && submission_image.is_approved == 0 && submission_image.send_date === null"
                                                                class="camera">
                                                                <div class="text-left">
                                                                    <i @click="showEdit(submission_image)"
                                                                        class="fas fa-camera mt-1 px-3 py-1 submission-edit"></i>
                                                                    <!-- <i @click="showEdit(submission)" class="far fa-edit mr-2 submission-edit"></i> -->
                                                                    <i @click="showDelete(submission_image.id)"
                                                                        class="fas fa-trash-alt submission-delete submission-edit ml-1 px-3 p-1"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div v-if="submission_image.send_date === null && submission_image.files.length > 0 && is_staff == true"
                                                            class="send-date">
                                                            <button @click.prevent="approvedSubmission(submission_image.id)"
                                                                class="btn btn-sm border rounded-pill btn-success"><i
                                                                    class="fas fa-paper-plane mr-1"></i>Gửi duyệt</button>
                                                        </div>

                                                    </div>
                                                    <div class="form-group">
                                                        <div class="image-container random">
                                                            <div class="mb-2 "
                                                                v-for="(image, index) in submission_image.files"
                                                                :key="index">
                                                                <div :class="['image', loading ? 'loading' : '']">
                                                                    <div class="img-relative mt-1">
                                                                        <img @click="showImageDialog(submission_image.files, submission_image.id, index)"
                                                                            :src="image.url" class="lazyload img-responsive"
                                                                            alt="" loading="lazy">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- <div class="mb-2" v-for="(image, index) in submission_image.files"
                                                        :key="index" >
                                                        <div :class="['image', loading ? 'loading' : '']">
                                                            <div class="img-ralative mt-1">
                                                                <img @click="showImageDialog(submission_image.files, submission_image.id)"
                                                                    :src="image.url" class="w-100 lazyload img-responsive" alt=""
                                                                    loading="lazy">
                                                                <div class="image-length" v-if="submission_image.files.length > 1"
                                                                    @click="showImageDialog(submission_image.files, submission_image.id)">
                                                                    <h4 class="text-light text-length ">
                                                                        +{{ submission_image.files.length }}</h4>
                                                                </div>
                                                            </div>
                                                        </div>
            
                                                    </div> -->
                                                        <!-- <div class="col-md-12 mb-2 image" v-if="submission_image.files.length == 0">
                                                            <div class="form-group img-ralative mt-1">
                                                                <img src="/submission_images/noimage.png" class="w-100 img-responsive"
                                                                    alt="" loading="lazy">
                                                            </div>
                                                        </div> -->
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="">
                                        <button @click="prevLoad" class="btn border btn-light rounded-pill"><i
                                                class="fas fa-angle-left"></i></button>
                                        <span class="p-2 px-3 mr-1 border"> {{ paginate.current_page }} </span> / <span> {{
                                            paginate.last_page }} </span>
                                        <button class="btn border btn-light rounded-pill" @click="loadMore">
                                            <i class="fas fa-angle-right"></i></button>
                                        <!-- <span> {{ count_item_current_page }} - {{ count_item_next_page }} </span> -->
                                        <button v-if="page < paginate.last_page" @click="loadMore"
                                            class="btn p-2 px-4 border rounded-pill">Trang tiếp theo</button>
                                        <button v-else @click="prevLoad" class="btn p-2 px-4 border rounded-pill">Trang
                                            trước</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            <!-- // Compare this snippet from resources\js\components\dms\JoinFormEvent .vue: -->
            <!-- <JoinForm ref="JoinFormEvent" :fetchSubmisstion="fetchSubmisstionStageImage" @programs="getProgram" @customers="getCustomer">
                </JoinForm> -->
        </div>


        <ImageDialog ref="ImageDialog"></ImageDialog>
        <ViewJoinProgram ref="ViewJoinProgram"></ViewJoinProgram>
        <SubmissionImageStage ref="SubmissionImageStage" :fetchSubmissionStageImage="fetchSubmisstionStageImage">
        </SubmissionImageStage>
    </div>
</template>
<script>
import toastr from 'toastr';
import JoinForm from './/JoinForm .vue';
import ImageDialog from './ImageDialog.vue';
import SubmissionImageStage from './SubmissionImageStage.vue';
import SearchCustomer from './search/SearchCustomer.vue';
import ViewJoinProgram from './dialog/ViewJoinProgram.vue';
import Treeselect from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';

export default {
    components: {
        JoinForm,
        ImageDialog,
        SubmissionImageStage,
        SearchCustomer,
        ViewJoinProgram,
        Treeselect
    },
    data() {
        return {
            loading: false,
            progress_success: false,
            current_user: window.Laravel.current_user,
            query: '',

            status_name: 'Tất cả',
            backgroud_color: '',
            status: [
                {
                    value: '',
                    name: 'Tất cả',
                    backgroud_color: ''
                },
                {
                    value: 'is_approved',
                    name: 'Đạt',
                    backgroud_color: 'bg-success text-white'
                },
                {
                    value: 'is_rejected',
                    name: 'Từ chối',
                    backgroud_color: 'bg-danger text-white'
                },
                {
                    value: 'is_pending',
                    name: 'Chờ duyệt',
                    backgroud_color: 'bg-warning text-white'
                },
                {
                    value: 'is_not_send',
                    name: 'Chưa gửi duyệt',
                    backgroud_color: 'bg-secondary text-white'
                },
                {
                    value: 'is_send',
                    name: 'Đã gửi duyệt',
                    backgroud_color: 'bg-primary text-white'
                }

            ],
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                },
                {
                    key: 'program_id',
                    label: 'Tên chương trình',
                },
                {
                    key: 'customer_id',
                    label: 'Khách hàng',
                },
                {
                    key: 'is_approved',
                    label: 'Trạng thái đã duyệt',
                },
                {
                    key: 'is_rejected',
                    label: 'Trạng thái từ chối',
                },
                {
                    key: 'note',
                    label: 'Mô tả',
                },
                {
                    key: 'action',
                    label: 'Actions',
                },
            ],
            show_search: false,
            is_staff: false,
            submission_images: [],
            programs: [],
            customers: [],
            token: "",
            filter: {
                program_id: null,
                customer_id: null,
                status: '',
                stage: '',
                start_date: '',
                end_date: '',
            },
            paginate: {
                current_page: 1,
                last_page: 1,
                total: 0,
            },
            page: 1,
            count_item_current_page: 1,
            count_item_next_page: 50,
            page_url_get_program: '/api/program',
            page_url_submission: '/api/submission-image-page',
            page_url_submission_image_send_date: '/api/submission-image-send-date',
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchSubmisstionStageImage();
        this.fetchProgram();

    },
    methods: {
       
        showJoinProgram() {
            this.$refs.ViewJoinProgram.openModal();
        },
        mapToTreeselect(data) {
            return data.map(item => {
                return {
                    id: item.id,
                    label: item.name,

                };
            });
        },
        loadMore() {
            if (this.paginate.current_page < this.paginate.last_page) {
                this.page++;
                this.fetchSubmisstionStageImage();

                window.scrollTo(0, 0);
            } else {
                this.showMessage('warning', 'Đã đến trang cuối cùng')
            }
        },
        prevLoad() {
            if (this.page > 1) {
                this.page--;
                this.fetchSubmisstionStageImage();
            } else {
                this.showMessage('warning', 'Đã đến trang đầu tiên')
            }
        },
        fetchSubmisstionStageImage(filter) {
            this.loading = true;
            if (filter === undefined) {
                filter = {
                    customer_id: '',
                }
            }
            const params = new URLSearchParams({
                program_id: this.filter.program_id,
                customer_id: filter.customer_id,
                status: this.filter.status,
                query: this.query,
                page: this.page,
                stage: this.filter.stage,
                start_date: this.filter.start_date,
                end_date: this.filter.end_date,
            });
            var page_url = this.page_url_submission + '?' + params.toString();
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {
                    this.submission_images = data.data;
                    // this.submission_images = [...this.submission_images, ...data.data];
                    this.is_staff = data.staff;
                    this.paginate.total = data.paginate.total;
                    this.paginate.current_page = data.paginate.current_page;
                    this.paginate.last_page = data.paginate.last_page;
                    this.loading = false;
                })
                .catch(err => {
                    console.log(err);
                    this.loading = false;

                });
        },
        fetchProgram() {

            var page_url = this.page_url_get_program;
            fetch(page_url, { headers: { Authorization: this.token } })
                .then(res => res.json())
                .then(res => {
                    this.programs = this.mapToTreeselect(res.data);
                }).catch(err => {
                    console.log(err);
                });
        },
        showFormEvent() {
            this.$refs.JoinFormEvent.show();
        },
        showEdit(submission_stage_image) {
            this.$refs.SubmissionImageStage.editSubmissionStageImage(submission_stage_image);
        },
        showDelete(id) {
            this.$refs.SubmissionImageStage.deleteSubmissionImage(id);
        },
        showImageDialog(image, submission_id, index) {
            this.$refs.ImageDialog.showImageDialog(image, submission_id, index);
        },
        searchFilter() {
            this.fetchSubmisstionStageImage();
        },
        resetFilter() {
            this.filter = {
                program_id: null,
                customer_id: null,
                status: '',
                stage: '',
                start_date: '',
                end_date: '',
            };
            this.status_name = 'Tất cả';
            this.query = '';
            this.backgroud_color = '';
            this.fetchSubmisstionStageImage();
            this.$refs.searchCustomer.reset();
        },
        getValuestatus(value) {
            this.filter.status = value.value;
            this.status_name = value.name;
            this.backgroud_color = value.backgroud_color;
            this.fetchSubmisstionStageImage();
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
        approvedSubmission(id) {
            var page_url = this.page_url_submission_image_send_date + '/' + id;
            fetch(page_url, {
                method: "PATCH",
                headers: {
                    "content-type": "application/json",
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {
                    this.showMessage('success', 'Duyệt', 'Gửi duyệt thành công');
                    this.fetchSubmisstionStageImage();
                })
                .catch(err => {
                    console.log(err);
                });
        },
        showStageImage() {
            this.$refs.SubmissionImageStage.showModal();
        },
        showSearch() {
            this.show_search = !this.show_search;
        }

    },
    computed: {
        rows() {
            return this.submission_images.length
        },
        hasMorePages() {
            return this.paginate.currentPage > this.paginate.lastPage;
        },


    }
}
</script>

<style lang="scss" scoped>
.join-program {
    background: rgb(242, 242, 242);
    border-radius: 10px;
    font-weight: 600;

    &:hover {
        color: blue;

    }
}

.nav-left {
    position: sticky;
    top: 80px;
    left: 0;

}

.image-container {
    display: flex;
    flex-wrap: wrap;
}

.image {
    width: 100%;
    /* Kích thước hiển thị của mỗi hình ảnh, trong trường hợp này là chia đôi */
    padding: 1px;
    position: relative;
    overflow: hidden;
}

.image::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    margin: 5px;
    border-radius: 5px;
    background-color: rgba(0, 0, 0, 0.1);
    /* Màu mờ mờ */
}

.img-relative {
    position: relative;
}

.img-relative img {
    object-fit: contain;
    height: auto;
}

.image-container.random {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    align-items: baseline;
}


.custom-container {
    overflow-x: hidden;
}

.program-time {
    font-size: 12px;
}

.img-ralative {
    position: relative;
    width: 100%;
    height: 100%;
}

.img-responsive {
    max-width: 100%;
    object-fit: cover;
    height: 100%;
    border-radius: 5px;

    &:hover {
        cursor: pointer;
        transition: all 0.5s ease-in-out;
        filter: blur(0.5px);
    }
}

.ribbon-wrapper.ribbon-lg {
    height: 80px;
    width: 155px;
}

.ribbon-wrapper {
    height: 70px;
    overflow: hidden;
    position: absolute;
    left: 0;
    top: 0;
    width: 70px;
    z-index: 10;
}

.ribbon-wrapper.ribbon-lg .ribbon {
    right: 0;
    top: 4px;
    width: 160px;
}

.ribbon-wrapper .ribbon {
    box-shadow: 0 0 3px rgba(0, 0, 0, .3);
    font-size: .8rem;
    line-height: 100%;
    padding: 0.375rem 0;
    position: relative;
    right: -2px;
    text-align: center;
    text-shadow: 0 -1px 0 rgba(0, 0, 0, .4);
    text-transform: uppercase;
    top: 10px;
    width: 90px;
}

@keyframes color-change {
    0% {
        color: black;
    }

    50% {
        color: rgb(3, 228, 63);
    }

    100% {
        color: black;
    }
}

.text-header {
    text-transform: uppercase;
    background-image: linear-gradient(-225deg,
            #fffddd 0%,
            rgb(226, 213, 75) 29%,
            #aeb322 67%,
            #f6ff00 100%);
    background-size: auto auto;
    background-clip: border-box;
    background-size: 200% auto;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: textclip 5s linear infinite;
    display: inline-block;
}

@keyframes textclip {
    to {
        background-position: 200% center;
    }
}

.submisstion-background {
    background: rgb(240, 242, 245);
    border: 1px solid rgba(255, 255, 255, 0.125);
    padding: 5px;
}

.submission-edit {
    color: black;
    background: linear-gradient(to right, #f2f2f2, #f2f2f2, #dbdbdb, #eaeaea);
    font-size: 15px;
    border-radius: 5px;

    &:hover {
        transform: scale(1.1);
        cursor: pointer;
    }
}

.submission-delete {
    color: crimson;

    &:hover {
        transform: scale(1.1);
        cursor: pointer;
    }
}

.image-length {
    background: rgb(255, 255, 255, 0.1);
    position: absolute;
    width: 100%;
    top: 0;
    height: 100%;

    &:hover {
        background: rgb(255, 255, 255, 0.2);
        cursor: pointer;
    }
}

.text-length {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}

.submission-header {
    height: 100%;
    color: white;
    background: white;
    border-radius: 10px;
}

.input-banner {
    border-radius: 30px;
    padding-left: 65px;
    padding-right: 140px !important;
    border-top-right-radius: 30px !important;
    border-bottom-right-radius: 30px !important;
}

.dms-text-content {
    text-align: center;
    color: #ec1f28;
    font-weight: 100;
    font-size: 24px;
}

@keyframes expandAnimation {
    0% {
        width: 0;
    }

    100% {
        width: 100%;
    }
}

.myDiv {
    width: 0;
    height: 3px;
    position: sticky;
    top: 0;
    z-index: 1;
    background-color: #ec1f28;
    border-radius: 10px;
    animation: expandAnimation 1s linear;
}

.icon-search {
    position: absolute;
    top: 22px;
    z-index: 15;
    left: 25px;
    font-size: 23px;
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

.btn-upload-img {
    position: fixed;
    z-index: 11;
    bottom: 10px;
    border-radius: 30px;
    right: 0;
    background: rgb(9, 171, 111);
    color: white;

    &:hover {
        background: rgb(20, 188, 125);
        color: white;
    }
}

.text-opacity {
    color: rgb(176, 176, 176);
}

.text-showroom {
    color: rgb(30, 67, 153);
    letter-spacing: 5px;
    position: relative;

    &::before {
        position: absolute;
        content: "";
        width: 182px;
        height: 2px;
        background: red;
        bottom: -3px;
    }
}

.input-stage:focus {
    outline: none;
    /* Remove default focus outline */
    border-color: blue;
    /* Change border color when focused */
    box-shadow: 0 0 2px #3a3a63;
    /* Add a box shadow when focused */
}

.text-font {
    font-family: "Roboto", "Arial", sans-serif;
    line-height: 1.5rem;
    font-weight: 400;
}

.sticky-search {
    position: sticky;
    top: 75px;
    z-index: 2;
    background: white;
}

.demo {
    width: 100%;
    position: absolute;
    z-index: 3;
    height: 100%;
}</style>
