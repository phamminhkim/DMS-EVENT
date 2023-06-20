<template>
    <div>
        <div class="">
            <div :class="loading == true ? 'myDiv' : ''"></div>

            <div class="container-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="shadow-sm p-3">
                            <div class=" d-flex justify-content-between">
                                <button @click="showSearch()"
                                    class="btn btn-outline  border p-2 px-4 rounded-pill btn-sm mb-1"
                                    style="font-weight:600;" type="button" data-toggle="collapse"
                                    data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                    Tìm kiếm chi tiết
                                    <span v-if="!show_search"><i class="fas fa-angle-down"></i></span>
                                    <span v-else><i class="fas fa-angle-up"></i></span>
                                </button>
                                <div class="">
                                    <button type="button"
                                        :class="'btn btn-sm custom-reset mb-1 px-4 p-2 dropdown-toggle ' + background_status"
                                        data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-star mr-2"></i>{{ text_status }}
                                    </button>
                                    <div class="dropdown-menu">
                                        <span class="dropdown-item" v-for="status in stage_status"
                                            @click="getValuestatus(status)">
                                            {{ status.text }} </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="collapseExample" v-if="show_search">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div>
                                            <label>Chương trình</label>
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <div class="form-group mb-1">
                                                        <treeselect v-model="filter.program_id" :multiple="false"
                                                            :options="programs" @input="fetchProgramStage()"
                                                            placeholder="Chọn chương trình..." id="program_id"
                                                            name="program_id" />
                                                    </div>
                                                    <div class="form-group d-flex justify-content-between">
                                                        <div class="">
                                                            <input type="number" class="border rounded p-1"
                                                                v-model="filter.stage" placeholder="Đợt.." id="name"
                                                                name="name" />
                                                            <button @click.prevent="reset()" class="btn custom-reset"><i
                                                                    class="fas fa-spinner"></i></button>
                                                        </div>
                                                        <button @click.prevent="fetchProgramStage()"
                                                            class="btn btn-warning btn-sm">
                                                            <i class="fas fa-search fa-rotate-90 mr-2"></i>Tìm kiếm
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-header">
                    <div class="form-group mb-1 text-right">
                        <button @click="showCreate()" class="btn btn-sm btn-info ">
                            <i class="fas fa-plus-circle"></i> Mở đợt đánh giá
                        </button>
                    </div>
                    <div class="form-group text-center">
                        <h4 class="text-uppercase font-weight-bold">Mở đợt đánh giá</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div :class="loading == true ? 'image' : ''">
                            <b-table responsive hover :bordered="true" small :items="program_stages" :fields="fields"
                                :current-page="current_page" :per-page="per_page">
                                <template #cell(index)="data">
                                    {{ data.index + (current_page - 1) * per_page + 1 }}
                                </template>
                                <template #cell(program_id)="data">
                                    <span v-if="data.item.program_id"> {{ data.item.program.name }} </span>
                                </template>
                                <template #cell(status)="data">
                                    <div>
                                        <button @click="showProgramStageOpen(data.item.id)"
                                            class="btn btn-sm px-4 mr-3 btn-open"
                                            :class="data.item.status == 1 ? ' btn-status ' : ' default '">Mở
                                        </button>
                                        <button @click="showProgramStageClose(data.item.id)"
                                            class="btn btn-sm px-4 btn-close"
                                            :class="data.item.status == 0 ? ' btn-status ' : ' default '">Đóng</button>

                                    </div>

                                </template>
                                <template #cell(action)="data">
                                    <button @click="showEdit(data.item)" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <button @click="showDelete(data.item.id)" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt"></i> Xóa
                                    </button>
                                </template>
                            </b-table>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="margin">
                                    <div class="btn-group">
                                        <label class="col-form-label-sm text-nowrap mr-1">Per page: </label>
                                        <b-form-select size="sm" v-model="per_page" :options="pageOptions">
                                        </b-form-select>
                                        <b-pagination v-model="current_page" :total-rows="rows" :per-page="per_page"
                                            size="sm" class="ml-1"></b-pagination>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        <ProgramStagesCreate ref="program_stages_create" :fetchProgramStages="fetchProgramStage" @programs="getPrograms">
        </ProgramStagesCreate>
    </div>
</template>

<script>
import ProgramStagesCreate from './ProgramStagesCreate.vue';
import Treeselect from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';

export default {
    components: {
        ProgramStagesCreate,
        Treeselect
    },
    data() {
        return {
            token: '',
            loading: false,
            show_search: false,
            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    sortable: true,
                    class: 'text-nowrap text-center'
                },
                {
                    key: 'program_id',
                    label: 'Chương trình',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'stage',
                    label: 'Đợt đánh giá',
                    sortable: true,
                    class: 'text-nowrap text-center'
                },
                {
                    key: 'note',
                    label: 'Ghi chú',
                    sortable: true,
                    class: 'text-nowrap text-center'
                },
                {
                    key: 'status',
                    label: 'Trạng thái',
                    sortable: true,
                    class: 'text-nowrap text-center'
                },
                {
                    key: 'action',
                    label: 'Hành động',
                    sortable: true,
                    class: 'text-nowrap text-center'
                },
            ],
            stage_status: [
                {
                    value: '',
                    text: 'Tất cả',
                    background: 'bg-light text-black'
                },
                {
                    value: 0,
                    text: 'Đang đóng',
                    background: 'bg-danger text-white'
                },
                {
                    value: 1,
                    text: 'Đang mở',
                    background: 'bg-success text-white'
                }
            ],

            filter: {
                program_id: null,
                status: '',
                stage: '',
            },
            text_status: 'Trạng thái',
            background_status: '',

            total: 0,
            per_page: 10,
            from: 1,
            to: 0,
            current_page: 1,
            pageOptions: [10, 50, 100, 500, { value: this.rows, text: "All" }],

            programs: [],
            program_stages: [],
            page_url_program_stages: "/api/program-stages",
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchProgramStage();
    },
    methods: {
        fetchProgramStage() {
            this.loading = true;
            const params = new URLSearchParams({
                status: this.filter.status,
                program_id: this.filter.program_id,
                stage: this.filter.stage,
            });
            var page_url = this.page_url_program_stages + '?' + params.toString();
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {

                    this.program_stages = data.data;
                    this.loading = false;
                })
                .catch(err => {
                    console.log(err);
                    this.loading = false;
                });
        },
        getValuestatus(status) {
            this.filter.status = status.value;
            this.text_status = status.text;
            this.background_status = status.background;
            this.fetchProgramStage();
        },
        showCreate() {
            this.$refs.program_stages_create.show();
        },
        showEdit(program_stage) {
            this.$refs.program_stages_create.editProgramStage(program_stage);
        },
        showDelete(id) {
            this.$refs.program_stages_create.deleteProgramStage(id);
        },
        showProgramStageOpen(id) {
            this.$refs.program_stages_create.programStageOpen(id);

        },
        showProgramStageClose(id) {
            this.$refs.program_stages_create.programStageClose(id);

        },
        getPrograms(programs) {
            this.programs = programs;
        },
        showSearch() {
            this.show_search = !this.show_search;
        },
        reset() {
            this.filter = {
                program_id: null,
                status: '',
                stage: '',
            };
            this.text_status = 'Trạng thái';
            this.background_status = '';
            this.fetchProgramStage();
        },
    },
    computed: {
        rows() {
            return this.program_stages.length;
        },
    },
}
</script>

<style lang="scss" scoped>
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

.image {

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
    border-radius: 5px;
    background-color: rgb(0 0 0 / 6%);
    /* Màu mờ mờ */
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

.btn-status {
    background: yellow;
    transition: all 0.3s;
    transform: translateX(20px);
    font-weight: 500;
    letter-spacing: 1px;

}

.default {
    background-color: rgba(171, 166, 166, 0.5);
    transition: all 0.3s;
    transform: translateX(0);

    &:hover {
        background: yellow;
        transform: scale(1.1);
    }
}

@media screen and (max-width: 768px) {
    .btn-status,
    .default {
        transform: none;
        /* Bỏ transform cho kích thước màn hình nhỏ */
    }
}</style>