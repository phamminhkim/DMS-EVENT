<template>
    <div>
        <div class="container-fluid">
            <div class="form-group row">
                <div class="col-md-12">
                    <div class="row shadow-sm p-3 ">
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
            <div class="container-header">
                <div class="form-group text-right">
                    <button v-if="is_staff == true" @click="showModal()" class="btn btn-sm btn-info">+ Đăng ký tham
                        gia</button>
                </div>
                <div class="form-group text-center">
                    <h4 class="text-uppercase font-weight-bold">Khách hàng tham gia chương trình</h4>
                </div>
            </div>
            <div class="container-body">
               
                <table v-if="loading">
                    <tbody>
                        <tr v-for="index in 10">
                            <td v-for="column in 5" :class="'td-' + column"><span></span></td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="!loading" class="bg-white">
                    <b-table striped hover small responsive :bordered="true" head-variant="light" :items="submissions"
                        :fields="fields" :current-page="current_page" :per-page="per_page" :filter="filter">
                        <template #cell(index)="data">
                             {{   data.index + (current_page - 1) * per_page +1 }}
                        </template>
                        <template #cell(program_id)="data">
                            <span v-if="data.item.program_id"> {{ data.item.program.name }} </span>
                        </template>
                        <template #cell(customer_id)="data">
                            <span v-if="data.item.customer_id"> {{ data.item.customer.name }} </span>
                        </template>
                        <template #cell(actions)="data">
                            <div class="d-flex justify-content-center">
                                <button  @click="showEdit(data.item)" class="btn btn-sm border-0 edit"> <i
                                        class="far fa-edit text-white font-weight-bold"></i></button>
                                <button @click="showDelete(data.item.id)" class="btn btn-sm ml-2 border-0 delete"><i
                                        class="fas fa-trash-alt text-white font-weight-bold"></i></button>

                            </div>
                        </template>
                    </b-table>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="margin">
                                <div class="btn-group">
                                    <label class="col-form-label-sm text-nowrap mr-1">Per page: </label>
                                    <b-form-select size="sm" v-model="per_page" :options="pageOptions">
                                    </b-form-select>
                                    <b-pagination v-model="current_page" :total-rows="rows" :per-page="per_page" size="sm"
                                        class="ml-1"></b-pagination>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <FormRegister ref="register" :submission_registers="submissions" :fetchSubmisstion="fetchSubmisstion"></FormRegister>
    </div>
</template>

<script>
import FormRegister from './FormRegister.vue';


export default {
    components: {
        FormRegister
    },
    data() {
        return {
            loading: false,
            is_staff: false,
            show_search: false,
            status_name: "Tất cả",
            filter_change: {
                status: '',
            },
            status: [
                {
                    value: '',
                    name: 'Tất cả'
                },
                {
                    value: 'is_approved',
                    name: 'Đã duyệt'
                },
                {
                    value: 'is_rejected',
                    name: 'Từ chối'
                },
                {
                    value: 'is_pending',
                    name: 'Chờ duyệt'
                },

            ],
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'program_id',
                    label: 'Chương trình',
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
                    key: 'note',
                    label: 'Ghi chú',
                    class: 'text-nowrap',
                    sortable: true,
                },
                {
                    key: 'actions',
                    label: 'Actions',
                    class: 'text-nowrap text-center'
                },
            ],
            token: "",
            filter: "",
            total: 0,
            per_page: 10,
            from: 1,
            to: 0,
            current_page: 1,
            pageOptions: [10, 50, 100, 500, { value: this.rows, text: "All" }],

            submissions: [],
            page_url_submission: '/api/submission',
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchSubmisstion();

    },
    methods: {
        fetchSubmisstion() {
            this.loading = true;
            const params = new URLSearchParams({
                status: this.filter_change.status,
            });
            var page_url = this.page_url_submission + '?' + params.toString();

            fetch(page_url, { headers: { Authorization: this.token } })
                .then(res => res.json())
                .then(data => {
                    this.submissions = data.data;
                    this.is_staff = data.staff;
                    this.loading = false;
                })
                .catch(err => {
                    console.log(err);
                    this.loading = false;

                });
        },
        showModal() {
            this.$refs.register.show();
        },
        showEdit(submission) {
            this.$refs.register.editSubmission(submission);
        },
        showDelete(submission_id) {
            this.$refs.register.deleteSubmission(submission_id);
        },
        getValuestatus(value) {
            this.filter_change.status = value.value;
            this.status_name = value.name
            this.fetchSubmisstion();
        },
        showSearch() {
            this.show_search = !this.show_search;
        }
    },
    computed: {
        rows() {
            return this.submissions.length
        }
    }
}
</script>

<style lang="scss" scoped>
.event-edit:hover {
    cursor: pointer;
}

.custom-reset {
    background: white;
    color: black;
    border: 0;
    border-radius: 30px !important;
    font-weight: 300;

    &:hover {
        color: #0071db;
    }
}

@keyframes moving-gradient {
    0% {
        background-position: -250px 0;
    }

    100% {
        background-position: 250px 0;
    }
}

table {
    width: 100%;

    tr {
        border-bottom: 1px solid rgba(0, 0, 0, .1);

        td {
            height: 50px;
            vertical-align: middle;
            padding: 8px;

            span {
                display: block;
            }

            &.td-1 {
                width: 20px;

                span {
                    width: 20px;
                    height: 20px;
                }
            }

            &.td-2 {
                width: 50px;

                span {
                    background-color: rgba(0, 0, 0, .15);
                    width: 50px;
                    height: 50px;
                }
            }

            &.td-3 {
                width: 400px;

                // padding-right: 100px;
                span {
                    height: 12px;
                    background: linear-gradient(to right, #eee 20%, #ddd 50%, #eee 80%);
                    background-size: 500px 100px;
                    animation-name: moving-gradient;
                    animation-duration: 1s;
                    animation-iteration-count: infinite;
                    animation-timing-function: linear;
                    animation-fill-mode: forwards;
                }
            }

            &.td-4 {}

            &.td-5 {
                width: 100px;

                span {
                    background-color: rgba(0, 0, 0, .15);
                    width: 100%;
                    height: 30px
                }
            }
        }
    }
}
.edit{
    background: linear-gradient(to top, #10d4ff, #2180a1);
    border-radius: 8px;
}
.delete{
    background: linear-gradient(to top, #ff0453, #ee0f56);
    border-radius: 8px;
}
</style>
