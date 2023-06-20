<template>
    <div>
        <div class="custom-container">
            <div class="border-bottom shadow-sm p-2">
                <div class="row" style="padding-right: 15px;">
                    <div class="col-md-12 mb-1">
                        <button @click="showSearch()" class="btn btn-outline mt-1 border p-2 px-4 rounded-pill btn-sm mb-1"
                            style="font-weight:600;" type="button" data-toggle="collapse" data-target="#collapseExample"
                            aria-expanded="false" aria-controls="collapseExample">
                            Tìm kiếm chi tiết
                            <span v-if="!show_search"><i class="fas fa-angle-down"></i></span>
                            <span v-if="show_search"><i class="fas fa-angle-up"></i></span>
                        </button>
                    </div>

                </div>
                <div class="form-group p-3 mb-0" id="collapseExample" v-if="show_search">
                    <div class="form-group mb-0 row">
                        <div class="col-md-6">
                            <div>
                                <label class="text-sm mt-2 flex-shrink-0 mr-2">
                                    <i class="far fa-id-card text-secondary"></i> Chương trình:
                                </label>
                                <treeselect class="rounded" v-model="program_id" :options="programs"
                                    :disable-branch-nodes="false" @input="fetchSubmisstionImage()" :multiple="false"
                                    id="program_id" name="program_id" placeholder="Chọn chương trình ...."></treeselect>
                                <label class="text-sm mt-2 flex-shrink-0 mr-2">
                                    <i class="far fa-id-card text-secondary"></i> Trạng thái:
                                </label>
                                <select class="form-control form-group mb-1" v-model="status"
                                    @change="fetchSubmisstionImage()">
                                    <option value="is_approved">Đã duyệt</option>
                                    <option value="is_rejected">Từ chối</option>
                                    <option value="pending">Chờ duyệt</option>
                                </select>
                              
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group text-sm mt-2 mr-2">
                                <label><i class="far fa-user mr-2"></i>Giám sát bán hàng:</label>
                                <treeselect v-model="user_GSBH" :options="users" :multiple="false" id="user" @input="fetchSubmisstionImage()"
                                    name="user" placeholder="Chọn giám sát bán hàng...." />
                              
                            </div>
                        </div>

                    </div>
                    <div class="form-group text-right">
                        <button @click="resetFilter()"
                            class="btn btn-sm btn-outline-secondary p-1 rounded-circle px-2"><i
                                class="fas fa-redo-alt"></i> </button>
                    </div>
                </div>
            </div>

            <div class="text-right form-group mt-1">
                <button @click="modalExcel()" class="btn btn-success btn-sm p-2 rounded-pill px-4 font-weight-normal">
                    <i class="fas fa-bars mr-2"></i>Tùy chọn trích xuất excel
                </button>
            </div>
            <div class="container-fluid ">
                <h4 class="text-uppercase text-center font-weight-bold">Đánh giá showroom duyệt cấp độ 2 </h4>
                <div class="container-header">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-profile-tab" data-toggle="tab"
                                data-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile"
                                aria-selected="false">Đã duyệt</button>

                            <button class="nav-link " id="nav-home-tab" data-toggle="tab" data-target="#nav-home"
                                type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                                Chờ xét duyệt
                                <span class="badge badge-danger"> {{ submisstionNotApproved.length }} </span>
                            </button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-profile" role="tabpanel"
                            aria-labelledby="nav-profile-tab">
                            <div class="form-group bg-white" :class="backdrop_filter == true ? 'filter' : ''">
                                <b-table responsive hover :sticky-header="true" :bordered="true" small
                                    :items="submisstionApproved" :filter="filter" :fields="fields"
                                    :current-page="current_page" :per-page="per_page">
                                    <template #cell(index)="data">
                                        {{ data.index + (current_page - 1) * per_page + 1 }}
                                    </template>
                                    <template #cell(program_id)="data">
                                        <span v-if="data.item.submission.program_id"> {{ data.item.submission.program.name
                                        }}
                                        </span>
                                    </template>
                                    <template #cell(user_id)="data">
                                        <span v-if="data.item.submission.user_id"> {{ data.item.submission.user.name }}
                                        </span>
                                    </template>
                                    <template #cell(customer_id)="data">
                                        <span v-if="data.item.submission.customer_id"> {{ data.item.submission.customer.name
                                        }}
                                        </span>
                                    </template>
                                    <template #cell(date_image)="data">
                                        <span v-if="data.item.files.length > 0">
                                            {{ data.item.files[0].updated_at | formatDate }}
                                        </span>
                                    </template>
                                    <template #cell(program_stage_id)="data">
                                        <span v-if="data.item.program_stage_id"> {{ data.item.program_stage.stage }} </span>
                                    </template>
                                    <template #cell(feedback_by)="data">
                                        <span v-if="data.item.feedback_by"> {{ data.item.feedback.name }} </span>
                                    </template>
                                    <template #cell(is_status)="data">
                                        <div class="text-center">

                                            <span
                                                v-if="data.item.is_approved_level2 == 0 && data.item.is_rejected_level2 == 1"
                                                class="text-danger"><i class="fas fa-times"></i></span>
                                            <span
                                                v-if="data.item.is_approved_level2 == 1 && data.item.is_rejected_level2 == 0"
                                                class="text-success"><i class="fas fa-check"></i></span>
                                        </div>
                                    </template>
                                    <template #cell(action)="data">
                                        <button @click.prevent="showViewSubmission(data.item)"
                                            class="btn btn-sm btn-info">Xem</button>
                                    </template>
                                </b-table>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-form-label-sm col-md-1" style="text-align: left" for="">Per
                                                page:</label>
                                            <div class="col-md-3">
                                                <b-form-select size="sm" v-model="per_page" :options="pageOptions">
                                                </b-form-select>
                                                <!-- <b-pagination v-model="current_page" :total-rows="rows"
                                                        :per-page="per_page" size="sm" class="ml-1"></b-pagination> -->
                                            </div>
                                            <label class="col-form-label-sm col-md-1" style="text-align: left"
                                                for=""></label>
                                            <div class="col-md-3">
                                                <b-pagination v-model="current_page" :total-rows="rows" :per-page="per_page"
                                                    size="sm" class="ml-1"></b-pagination>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade " id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                            <div class="bg-white text-right p-3">
                                <button @click="selectAllRows" class="btn btn-info btn-sm">Chọn tất cả</button>
                                <button @click="clearSelected" class="btn btn-outline-secondary btn-sm">Hủy</button>
                            </div>
                            <div v-if="selected.length > 0" class="bg-white w-50 float-right">
                                <button @click.prevent="allApproved()" class="btn btn-sm btn-success w-100"><i
                                        class="fas fa-check mr-2"></i> Duyệt <span class="badge badge-secondary">{{
                                            selected.length }}</span> (items) đã chọn</button>
                            </div>

                            <div class="form-group bg-white" :class="backdrop_filter == true ? 'filter' : ''">
                                <b-table responsive hover :sticky-header="true" small :items="submisstionNotApproved"
                                    :filter="filter" ref="selectableTable" selectable :select-mode="selectMode"
                                    @row-selected="onRowSelected" :fields="fields_pending">

                                    <template #cell(selectRow)="{ rowSelected }">
                                        <template v-if="rowSelected">
                                            <span aria-hidden="true" class="text-center">
                                                &check;
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span aria-hidden="true">
                                                &nbsp;&nbsp;&nbsp;
                                            </span>
                                        </template>
                                    </template>
                                    <template #cell(index)="data">
                                        <span> {{ data.index + 1 }} </span>
                                    </template>
                                    <template #cell(program_id)="data">
                                        <span v-if="data.item.submission.program_id"> {{ data.item.submission.program.name
                                        }}
                                        </span>
                                    </template>
                                    <template #cell(user_id)="data">
                                        <span v-if="data.item.submission.user_id"> {{ data.item.submission.user.name }}
                                        </span>
                                    </template>
                                    <template #cell(customer_id)="data">
                                        <span v-if="data.item.submission.customer_id"> {{ data.item.submission.customer.name
                                        }}
                                        </span>
                                    </template>
                                    <template #cell(program_stage_id)="data">
                                        <span v-if="data.item.program_stage_id"> {{ data.item.program_stage.stage }} </span>
                                    </template> <template #cell(date_image)="data">
                                        <span v-if="data.item.files.length > 0">
                                            {{ data.item.files[0].updated_at | formatDate }}
                                        </span>
                                    </template>
                                    <template #cell(feedback_by)="data">
                                        <span v-if="data.item.feedback_by"> {{ data.item.feedback.name }} </span>
                                    </template>
                                    <template #cell(is_status)="data">
                                        <div class="text-center">
                                            <button @click.prevent="approvedSubmission(data.item.id)"
                                                v-if="data.item.is_approved_level2 == 0 && data.item.is_rejected_level2 == 0"
                                                class="btn btn-sm btn-success">Duyệt</button>
                                            <button @click.prevent="showDialogRejected(data.item)"
                                                v-if="data.item.is_approved_level2 == 0 && data.item.is_rejected_level2 == 0"
                                                class="btn btn-sm btn-danger">Từ chối</button>

                                        </div>
                                    </template>
                                    <template #cell(action)="data">
                                        <button @click.prevent="showViewSubmission(data.item)"
                                            class="btn btn-sm btn-outline-info"><i class="fas fa-eye mr-2"></i>Xem</button>
                                    </template>
                                </b-table>

                            </div>
                        </div>

                    </div>
                </div>
            </div>


        </div>
        <ViewSubmission ref="viewSubmission"></ViewSubmission>
        <DialogRejected ref="dialogRejected" :fetchSubmisstion="fetchSubmisstionImage" :component="'suppervision'">
        </DialogRejected>
        <ExportExcel ref="dialogExcel"></ExportExcel>
    </div>
</template>

<script>
import * as XLSX from 'xlsx';
import axios from 'axios';
import Treeselect from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
import ViewSubmission from './ViewSubmission.vue';
import DialogRejected from './DialogRejected.vue';
import ExportExcel from './excel/ExportExcel.vue';

export default {
    components: {
        ViewSubmission,
        DialogRejected,
        ExportExcel,
        Treeselect
    },
    data() {
        return {
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'program_id',
                    label: 'Tên chương trình',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'user_id',
                    label: 'Nhân viên',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'customer_id',
                    label: 'Khách hàng',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'program_stage_id',
                    label: 'Đợt',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'feedback_by',
                    label: 'GSBH',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'is_status',
                    label: 'Trạng thái',
                    class: 'text-nowrap text-center',
                    sortable: true,
                },
                {
                    key: 'date_image',
                    label: 'Ngày chụp ảnh',
                    class: 'text-center text-nowrap',
                    sortable: true,
                },
                {
                    key: 'feedback_content',
                    label: 'Feedback',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'action',
                    label: 'Actions',
                    class: 'text-nowrap',
                    sortable: true,
                },
            ],
            fields_pending: [

                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-center text-nowrap',
                    sortable: true,
                },
                {
                    key: 'program_id',
                    label: 'Tên chương trình',
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
                    key: 'customer_id',
                    label: 'Khách hàng',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'program_stage_id',
                    label: 'Đợt',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'feedback_by',
                    label: 'GSBH',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'is_status',
                    label: 'Trạng thái',
                    class: 'text-center text-nowrap',
                    sortable: true,
                },
                {
                    key: 'selectRow',
                    label: '',
                    sortable: true,
                },
                {
                    key: 'date_image',
                    label: 'Ngày chụp ảnh',
                    class: 'text-center text-nowrap',
                    sortable: true,
                },
                {
                    key: 'note',
                    label: 'Mô tả',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'action',
                    label: 'Actions',
                    class: 'text-nowrap',
                    sortable: true,
                },
            ],
            submission_images: [],
            programs: [],
            users: [],

            per_page: 10,
            from: 1,
            to: 0,
            current_page: 1,
            pageOptions: [10, 50, 100, 500, { value: this.rows, text: "All" }],
            pagination: {
                total: 0,
                per_page: 2,
                from: 1,
                to: 0,
                current_page: 1,
            },

            filter: '',
            status: '',
            program_id: null,
            backdrop_filter: false,
            show_search: false,
            user_GSBH: null,

            selectMode: 'multi',
            selected: [],

            token: "",
            page_url_submission: '/api/submission-list-image',
            page_url_submission_is_approved: '/api/submission-is_approved_level2',
            page_url_all_approved_level2: '/api/all-approved-level2',
            page_url_program: "/api/program",
            page_urrl_get_user: '/api/user',


        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchSubmisstionImage();
        this.fetchProgram();
        this.fetchUser();
    },
    methods: {
        modalExcel() {
            this.$refs.dialogExcel.showModal();
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
        fetchSubmisstionImage() {
            this.backdrop_filter = true;
            const params = new URLSearchParams({
                status: this.status,
                program_id: this.program_id,
                user_GSBH: this.user_GSBH,
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
                    this.submission_images = data.data.filter(item => item.files.length > 0);
                    this.backdrop_filter = false;
                    // this.submission_images = data.data;
                })
                .catch(err => {
                    this.backdrop_filter = false;
                    console.log(err);
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
                })
                .catch(err => {
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
        approvedSubmission(id) {
            var page_url = this.page_url_submission_is_approved + '/' + id;
            fetch(page_url, {
                method: "PUT",
                headers: {
                    "content-type": "application/json",
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {
                    this.showMessage('success', 'Duyệt', 'Duyệt thành công');
                    this.fetchSubmisstionImage();
                })
                .catch(err => {
                    console.log(err);
                });
        },
        allApproved() {
            this.backdrop_filter = true;
            var page_url = this.page_url_all_approved_level2;
            fetch(page_url, {
                method: "POST",
                headers: {
                    "content-type": "application/json",
                    Authorization: this.token
                },
                body: JSON.stringify(this.selected)
            })
                .then(res => res.json())
                .then(data => {
                    this.showMessage('success', 'Duyệt', 'Duyệt thành công');
                    this.fetchSubmisstionImage();
                    this.reset();
                    this.backdrop_filter = false;
                })
                .catch(err => {
                    console.log(err);
                    this.backdrop_filter = false;
                });
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
        showViewSubmission(submission) {
            this.$refs.viewSubmission.viewSubmission(submission);
        },
        showDialogRejected(submission) {
            this.$refs.dialogRejected.show(submission);
        },
        onRowSelected(items) {
            this.selected = items;
        },
        selectAllRows() {
            this.$refs.selectableTable.selectAllRows();
        },
        clearSelected() {
            this.$refs.selectableTable.clearSelected();
        },
        reset() {
            // this.$refs.selectableTable.reset();
            this.selected = [];
        },
        resetFilter() {
            this.filter = '';
            this.status = '';
            this.program_id = null;
            this.user_GSBH = null;
            this.fetchSubmisstionImage();
        },
        showSearch() {
            this.show_search = !this.show_search;
        }

    },
    computed: {
        submisstionApproved() {
            return this.submission_images.filter(submission => submission.is_approved_level2 == 1 || submission.is_rejected_level2 == 1);
        },
        submisstionNotApproved() {
            return this.submission_images.filter(submission => submission.is_approved == 1 && submission.is_approved_level2 == 0 && submission.is_rejected_level2 == 0);
        },
        rows() {
            return this.submisstionApproved.length;
        }

    }
}

</script>

<style lang="scss" scoped>
.custom-container {
    overflow-x: hidden;
}

.filter {
    filter: blur(3px);
}
</style>
