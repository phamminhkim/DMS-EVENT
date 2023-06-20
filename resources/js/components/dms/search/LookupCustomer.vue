<template>
    <div>
        <div :class="loading == true ? 'myDiv' : ''"></div>
        <div class="container-fluid">
            <div class="container-header mt-4">
                <div class="form-group row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <h4 class="text-uppercase font-weight-bold">Tra cứu khách hàng</h4>
                        <div class="input-group mb-3">
                            <input v-model="filter.query_dms_or_name" type="text" class="form-control"
                                placeholder="Nhập tên hoặc mã khách hàng..." @keyup.enter="fetchProgramStage()"
                                style="padding-right:40px">
                            <i @click="resetFilter()" class="fas fa-times reset-filter"></i>
                            <div class="input-group-append">
                                <button @click="fetchProgramStage()" class="input-group-text bg-warning text-white"
                                    id="basic-addon2"><i class="fas fa-search mr-2"></i> Tìm kiếm</button>
                            </div>
                        </div>
                        <div class="text-right">
                            <button @click="resetFilter()" class="btn custom-reset"><i class="fas fa-spinner"></i></button>

                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>

            </div>
            <div class="container-body">
                <div class="form-group row">
                    <div class="col-md-1"></div>
                    <div class="col-12 col-md-12 col-sm-12 px-lg-5">
                        <span class="font-italic text-opacity"> {{ lookup_customers.length }} of {{ paginate.total }} items
                        </span>
                        <div class="card bg-white shadow">
                            <div class="card-header">
                                <h5>Danh sách</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group border-bottom" v-for="(customer, index) in lookup_customers"
                                    :key="index">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group d-flex">
                                                <div>
                                                    <span class="font-weight-bold mr-4"> {{ index + (paginate.current_page -
                                                        1) * perPage + 1 }}.</span>
                                                </div>
                                                <div>
                                                    <p class="font-weight-bold"> {{ customer.name }} </p>
                                                    <small class="text-secondary">Mã DMS: {{ customer.dms_code }}
                                                    </small><br>
                                                    <small class="text-secondary">Địa chỉ: {{ customer.address }}
                                                    </small><br>
                                                    <small v-if="customer.user_id" class="text-secondary">NVBH: {{
                                                        customer.user.staff_code }} - {{ customer.user.name }} </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-center">
                                                <span v-if="customer.submissions.length > 0"
                                                    class="text-uppercase text-secondary">
                                                    Đã đăng ký tham gia các chương trình
                                                </span>
                                                <span v-else class="text-uppercase text-secondary">
                                                    Chưa đăng ký tham gia chương trình nào
                                                </span>
                                            </div>
                                            <div class="form-group mb-0" v-for="(sub, index_sub) in customer.submissions">
                                                <span class="text-secondary">
                                                    {{ index_sub + 1 }}.

                                                </span>
                                                <small class="font-weight-bold" v-if="sub.program_id"> {{ sub.program.name
                                                }} </small>
                                                <p class="mb-0 ml-4 d-flex align-items-baseline justify-content-between"
                                                    v-for="(sub_image, count) in sub.submission_images">
                                                    <span v-if="sub_image.program_stage_id">
                                                        <small class="text-opacity">
                                                            <small>
                                                                <i class="far fa-bookmark" style="width: 13px;"></i>Đợt:
                                                            </small>
                                                            {{ sub_image.program_stage.stage }}
                                                            <small
                                                                :class="sub_image.program_stage.status == 1 ? 'status' : ''">
                                                                {{ sub_image.program_stage.status == 1 ? ' (Đang mở)' :
                                                                    '(Đang đóng)' }}
                                                                    <small class="text-opacity">
                                                                        Lần: {{ getUpCount(sub.submission_images, count) }}
                                                                    </small>
                                                            </small>
                                                        </small>
                                                    </span>

                                                    <span
                                                        v-if="sub_image.is_approved_level2 == 1 && sub_image.is_approved == 1"
                                                        class="badge badge-success font-weight-bold">
                                                        Đạt
                                                    </span>
                                                    <span
                                                        v-if="sub_image.is_rejected_level2 == 1 && sub_image.is_approved == 1"
                                                        class="badge badge-danger font-weight-bold">
                                                        Từ chối
                                                    </span>
                                                    <span
                                                        v-if="sub_image.is_approved_level2 == 0 && sub_image.is_rejected_level2 == 0 && sub_image.is_rejected == 1"
                                                        class="badge badge-danger font-weight-bold">
                                                        Từ chối
                                                    </span>
                                                    <span
                                                        v-if="sub_image.is_approved_level2 == 0 && sub_image.is_rejected_level2 == 0 && sub_image.is_approved == 1"
                                                        class="badge badge-warning text-white font-weight-bold">
                                                        Chờ xét duyệt
                                                    </span>
                                                    <span
                                                        v-if="sub_image.is_approved_level2 == 0 && sub_image.is_rejected_level2 == 0 && sub_image.is_approved == 0 && sub_image.is_rejected == 0 && sub_image.send_date !== null"
                                                        class="badge badge-primary font-weight-bold">
                                                        Đã gửi duyệt
                                                    </span>
                                                    <span v-if="sub_image.send_date == null"
                                                        class="badge badge-secondary font-weight-bold">
                                                        Chưa gửi duyệt
                                                    </span>
                                                </p>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
                <div class="form-group">
                    <button @click="prevLoad" class="btn border btn-light rounded-pill"><i
                            class="fas fa-angle-left"></i></button>
                    <span class="p-2 px-3 mr-1 border"> {{ paginate.current_page }} </span> / <span> {{
                        paginate.last_page }} </span>
                    <button class="btn border btn-light rounded-pill" @click="loadMore">
                        <i class="fas fa-angle-right"></i></button>
                    <!-- <span> {{ count_item_current_page }} - {{ count_item_next_page }} </span> -->
                    <button v-if="page < paginate.last_page" @click="loadMore"
                        class="btn p-2 px-4 border rounded-pill">Trang tiếp theo</button>
                    <button v-else @click="prevLoad" class="btn p-2 px-4 border rounded-pill">Trang trước</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            token: '',
            loading: false,
            filter: {
                query_dms_or_name: '',
            },
            upCounts: {},
            page: 1,
            perPage: 10,
            paginate: {
                total: 0,
                current_page: 0,
                last_page: 0,
            },
            tess: 0,
            lookup_customers: [],
            page_url_lookup_customer: '/api/lookup-customer',
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchProgramStage();

    },
    methods: {
        getUpCount(submissionImages, currentIndex) {
            let count = 0;
            for (let i = 0; i <= currentIndex; i++) {
                if (submissionImages[i].program_stage_id === submissionImages[currentIndex].program_stage_id) {
                    count++;
                }
            }
            return count;
        },
        fetchProgramStage() {
            this.loading = true;
            const params = new URLSearchParams({
                page: this.page,
                query_dms_or_name: this.filter.query_dms_or_name,
            });
            var page_url = this.page_url_lookup_customer + '?' + params.toString();
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {
                    this.lookup_customers = data.data.data;
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
        loadMore() {
            if (this.paginate.current_page < this.paginate.last_page) {
                this.page++;
                this.fetchProgramStage();
                window.scrollTo(0, 0);
            } else {
                this.showMessage('warning', 'Đã đến trang cuối cùng')
            }
        },
        prevLoad() {
            if (this.page > 1) {
                this.page--;
                this.fetchProgramStage();
            } else {
                this.showMessage('warning', 'Đã đến trang đầu tiên')
            }
        },
        resetFilter() {
            this.filter.query_dms_or_name = '';
            this.fetchProgramStage();
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
    },
}
</script>
<style lang="scss" scoped>
.text-opacity {
    color: rgb(176, 176, 176);
}

.status {
    color: yellow;
    font-weight: 500;
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

@keyframes expandAnimation {
    0% {
        width: 0;
    }

    100% {
        width: 100%;
    }
}

.reset-filter {
    position: absolute;
    right: 125px;
    z-index: 3;
    cursor: pointer;
    color: gray;
    top: 12px;
    visibility: hidden;
    opacity: 0;
    transition: opacity .3s ease-out, visibility .3s ease-out;
}

input:focus+.reset-filter {
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