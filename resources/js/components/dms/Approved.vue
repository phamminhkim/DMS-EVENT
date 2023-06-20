<template>
    <div>

        <div class="border-bottom form-group shadow-sm p-2">
            <div class="row" style="padding-right: 15px;">
                <div class="col-md-12 mb-1">
                    <div class="d-flex justify-content-between">
                        <button @click="showSearch()" class="btn btn-outline mt-1 border p-2 px-4 rounded-pill btn-sm mb-1"
                            style="font-weight:600;" type="button" data-toggle="collapse" data-target="#collapseExample"
                            aria-expanded="false" aria-controls="collapseExample">
                            Tìm kiếm chi tiết
                            <span v-if="!show_search"><i class="fas fa-angle-down"></i></span>
                            <span v-if="show_search"><i class="fas fa-angle-up"></i></span>
                        </button>
                        <button type="button" :class="'btn btn-sm mb-1 px-4 custom-reset dropdown-toggle ' + background_status"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span><i class="far fa-star mr-2"></i> {{ status_name }} </span>
                        </button>
                        <div class="dropdown-menu">
                            <a v-for="stt in approved_status" class="dropdown-item" @click.prevent="getValuestatus(stt)">
                                {{ stt.text }}
                            </a>
                        </div>
                    </div>

                </div>

            </div>
            <div class="form-group p-3 mb-0" id="collapseExample" v-if="show_search">
                <div class="row">
                    <div class="col-md-3">

                    </div>
                    <div class="col-md-6">
                        <input type="search" class="form-control" v-model="filter" placeholder="Tìm kiếm...."
                            aria-label="Search" style="border-radius:30px;padding-left: 35px">
                        <i class="fas fa-search fa-rotate-90 text-secondary"
                            style="position: absolute;top: 12px;left: 27px;"></i>

                    </div>
                    <div class="col-md-3">
                        <button @click="resetFilter()"
                        class="btn btn-sm btn-outline-secondary p-1 rounded-circle mt-1 px-2"><i
                            class="fas fa-redo-alt"></i> </button>
                    </div>
                </div>

            </div>
        </div>
        <div class="container-header">
            <div class="mt-2 text-center">
                <h4 class="text-uppercase font-weight-bold">Đánh giá showroom </h4>
            </div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-profile-tab" data-toggle="tab" data-target="#nav-profile"
                        type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Đã duyệt</button>

                    <button class="nav-link " id="nav-home-tab" data-toggle="tab" data-target="#nav-home" type="button"
                        role="tab" aria-controls="nav-home" aria-selected="true">
                        Chờ xét duyệt
                        <span class="badge badge-danger"> {{ submisstionNotApproved.length }} </span>
                    </button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <div class="form-group bg-white" :class="backdrop_filter == true ? 'filter' : ''">
                        <b-table responsive hover :bordered="true" :sticky-header="true" small :items="submisstionApproved"
                            :filter="filter" :fields="fields" :current-page="current_page" :per-page="per_page">
                            <template #cell(index)="data">
                                {{ data.index + (current_page - 1) * per_page + 1 }}
                            </template>
                            <template #cell(program_id)="data">
                                <span v-if="data.item.submission.program_id"> {{ data.item.submission.program.name }}
                                </span>
                            </template>
                            <template #cell(user_id)="data">
                                <span v-if="data.item.submission.user_id"> {{ data.item.submission.user.name }} </span>
                            </template>
                            <template #cell(customer_id)="data">
                                <span v-if="data.item.submission.customer_id"> {{ data.item.submission.customer.name }}
                                </span>
                            </template>
                            <template #cell(date_image)="data">
                                <span v-if="data.item.files.length > 0">
                                    {{ data.item.files[0].updated_at | formatDate }}
                                </span>
                            </template>
                            <template #cell(is_status)="data">
                                <div class="text-center">
                                    <button @click.prevent="approvedSubmission(data.item.id)"
                                        v-if="data.item.is_approved == 0 && data.item.is_rejected == 0"
                                        class="btn btn-sm btn-success">Duyệt</button>
                                    <button @click.prevent="rejectedSubmission(data.item.id)"
                                        v-if="data.item.is_approved == 0 && data.item.is_rejected == 0"
                                        class="btn btn-sm btn-danger">Từ chối</button>
                                    <span v-if="data.item.is_approved == 0 && data.item.is_rejected == 1"
                                        class="text-danger"><i class="fas fa-times"></i></span>
                                    <span v-if="data.item.is_approved == 1 && data.item.is_rejected == 0"
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
                                    <label class="col-form-label-sm col-md-1" style="text-align: left" for=""></label>
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
                                class="fas fa-check mr-2"></i> Duyệt <span class="badge badge-secondary">{{ selected.length
                                }}</span> (items) đã chọn</button>
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
                                <span v-if="data.item.submission.program_id"> {{ data.item.submission.program.name }}
                                </span>
                            </template>
                            <template #cell(user_id)="data">
                                <span v-if="data.item.submission.user_id"> {{ data.item.submission.user.name }} </span>
                            </template>
                            <template #cell(customer_id)="data">
                                <span v-if="data.item.submission.customer_id"> {{ data.item.submission.customer.name }}
                                </span>
                            </template>
                            <template #cell(program_stage_id)="data">
                                <span v-if="data.item.program_stage_id"> {{ data.item.program_stage.stage }} </span>
                            </template>
                            <template #cell(date_image)="data">
                                <span v-if="data.item.files.length > 0">
                                    {{ data.item.files[0].updated_at | formatDate }}
                                </span>
                            </template>
                            <template #cell(send_date)="data">
                                <span>
                                    {{ data.item.send_date | formatDate }}
                                </span>
                            </template>
                            <template #cell(is_status)="data">
                                <div class="text-center">
                                    <button @click.prevent="approvedSubmission(data.item.id)"
                                        v-if="data.item.is_approved == 0 && data.item.is_rejected == 0"
                                        class="btn btn-sm btn-success">Duyệt</button>
                                    <button @click.prevent="showDialogRejected(data.item)"
                                        v-if="data.item.is_approved == 0 && data.item.is_rejected == 0"
                                        class="btn btn-sm btn-danger">Từ chối</button>
                                    <span v-if="data.item.is_approved == 0 && data.item.is_rejected == 1"
                                        class="text-danger"><i class="fas fa-times"></i></span>
                                    <span v-if="data.item.is_approved == 1 && data.item.is_rejected == 0"
                                        class="text-success"><i class="fas fa-check"></i></span>
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
        <div class="container-body">

        </div>
        <ViewSubmission ref="viewSubmission"></ViewSubmission>
        <DialogRejected ref="dialogRejected" :fetchSubmisstion="fetchSubmisstion" :component="'approved'"></DialogRejected>
    </div>
</template>

<script>
import ViewSubmission from './ViewSubmission.vue';
import DialogRejected from './DialogRejected.vue';

export default {
    components: {
        ViewSubmission,
        DialogRejected
    },
    data() {
        return {
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-nowrap'
                },
                {
                    key: 'program_id',
                    label: 'Tên chương trình',
                    class: 'text-nowrap'
                },
                {
                    key: 'user_id',
                    label: 'Nhân viên',
                    class: 'text-nowrap'
                },
                {
                    key: 'customer_id',
                    label: 'Khách hàng',
                    class: 'text-nowrap'
                },
                {
                    key: 'is_status',
                    label: 'Trạng thái',
                    class: 'text-center text-nowrap'
                },
                {
                    key: 'date_image',
                    label: 'Ngày chụp ảnh',
                    class: 'text-center text-nowrap'
                },
                {
                    key: 'feedback_content',
                    label: 'Feedback',
                    class: 'text-nowrap'
                },
                {
                    key: 'action',
                    label: 'Actions',
                    class: 'text-nowrap'
                },
            ],
            fields_pending: [

                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-nowrap'
                },
                {
                    key: 'program_id',
                    label: 'Tên chương trình',
                    class: 'text-nowrap'
                },
                {
                    key: 'user_id',
                    label: 'Nhân viên',
                    class: 'text-nowrap'
                },
                {
                    key: 'customer_id',
                    label: 'Khách hàng',
                    class: 'text-nowrap'
                },
                {
                    key: 'program_stage_id',
                    label: 'Đợt',
                    class: 'text-center text-nowrap'
                },
                {
                    key: 'is_status',
                    label: 'Trạng thái',
                    class: 'text-center text-nowrap'
                },
                {
                    key: 'selectRow',
                    label: '',
                    class: 'text-nowrap'
                },
                {
                    key: 'date_image',
                    label: 'Ngày chụp ảnh',
                    class: 'text-center text-nowrap'
                },
                {
                    key: 'send_date',
                    label: 'Ngày gửi duyệt',
                    class: 'text-center text-nowrap'
                },
                {
                    key: 'note',
                    label: 'Mô tả',
                    class: 'text-nowrap'
                },
                {
                    key: 'action',
                    label: 'Actions',
                    class: 'text-nowrap'
                },
            ],
            status_name: 'Trạng thái',
            background_status: '',
            approved_status: [
                {
                    value: 'is_approved',
                    text: 'Đã duyệt',
                    background: 'bg-success text-white'
                },
                {
                    value: 'is_rejected',
                    text: 'Từ chối',
                    background: 'bg-danger text-white'
                },
                {
                    value: '',
                    text: 'Tất cả',
                    background: 'bg-light text-black'
                }
            ],

            submissions: [],

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
            backdrop_filter: false,
            show_search: false,

            selectMode: 'multi',
            selected: [],

            token: "",
            page_url_submission: '/api/submission-list-image',
            page_url_submission_is_approved: '/api/submission-is_approved',
            page_url_all_approved: '/api/all-approved',

        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchSubmisstion();
    },
    methods: {
        fetchSubmisstion() {
            this.backdrop_filter = true;
            const params = new URLSearchParams({
                status: this.status,
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
                    this.submissions = data.data.filter(item => item.files.length > 0);
                    this.backdrop_filter = false;
                    // this.submissions = data.data;
                })
                .catch(err => {
                    this.backdrop_filter = false;
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
                    this.fetchSubmisstion();
                })
                .catch(err => {
                    console.log(err);
                });
        },
        allApproved() {
            this.backdrop_filter = true;
            var page_url = this.page_url_all_approved;
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
                    this.fetchSubmisstion();
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
            this.status_name = 'Trạng thái';
            this.background_status = '';
            this.fetchSubmisstion();
        },
        getValuestatus(value) {
            this.status = value.value;
            this.status_name = value.text;
            this.background_status = value.background;
            this.fetchSubmisstion();
        },
        showSearch() {
            this.show_search = !this.show_search;
        }

    },
    computed: {
        submisstionApproved() {
            return this.submissions.filter(submission => submission.is_approved == 1 || submission.is_rejected == 1);
        },
        submisstionNotApproved() {
            return this.submissions.filter(submission => submission.is_approved == 0 && submission.is_rejected == 0 && submission.send_date !== null);
        },
        rows() {
            return this.submisstionApproved.length;
        }

    }
}

</script>

<style lang="scss" scoped>
.filter {
    filter: blur(3px);
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
