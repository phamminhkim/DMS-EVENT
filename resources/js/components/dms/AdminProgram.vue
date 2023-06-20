<template>
    <div>
        <div class="custom-container">
            <div class="border-bottom shadow-sm p-2">
                <div class="row" style="padding-right: 15px;">
                    <div class="col-md-6 mb-1">
                        <button @click="showSearch()" class="btn btn-outline mt-1 border p-2 px-4 rounded-pill btn-sm mb-1"
                        style="font-weight:600;" type="button" data-toggle="collapse" data-target="#collapseExample"
                        aria-expanded="false" aria-controls="collapseExample">
                        Tìm kiếm chi tiết
                        <span v-if="!show_search"><i class="fas fa-angle-down"></i></span>
                        <span v-if="show_search"><i class="fas fa-angle-up"></i></span>
                    </button>
                    </div>
                    <div class="col-md-6">
                        <input class="form-control mt-1" placeholder="Tìm kiếm..."
                        v-model="filter.query"  style="border-radius:30px;padding-left: 35px" />
                        <i class="fas fa-search fa-rotate-90 text-secondary"
                        style="position: absolute;top: 16px;left: 27px;"></i>
                    </div>
                </div>
                <div class="form-group p-3" id="collapseExample" v-if="show_search">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-2.5">
                                    <label class="ml-3"><i class="fas fa-feather-alt mr-1"></i>Chương trình</label>
                                </div>
                                <div class="col-md-8">
                                    <treeselect v-model="filter.program_id" :options="programs"
                                        @input="fetchProgramAdmin()" :multiple="false" id="program_id" name="program_id"
                                        placeholder="Chọn chương trình đăng ký...."></treeselect>
                                </div>
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
            <div class="container-header mt-2">
                <div class="form-group mb-0 text-right mt-1">
                    <button v-if="GSBH === false" @click="showFormAdminProgram()" class="btn btn-sm btn-info">Thêm người
                        quản lý chương trình</button>
                </div>
               
            </div>
            <div class="container-body p-3">
                <div :class="loading == true ? 'myDiv' : ''"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group text-center">
                            <h4 class="text-uppercase font-weight-bold">Danh sách Admin chương trình
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <b-table responsive hover striped small  :bordered="true" :items="program_admins" :filter="filter.query"
                        :fields="fields" :current-page="current_page" :per-page="per_page">
                        <template #cell(index)="data">
                            {{ data.index + (current_page - 1) * per_page + 1 }}
                        </template>
                        <template #cell(program_id)="data">
                            <span v-if="data.item.program_id"> {{ data.item.program.name }} </span>
                        </template>
                        <template #cell(user_id)="data">
                            <span v-if="data.item.user_id"> {{ data.item.user.name }} </span>
                        </template>
                        <template #cell(actions)="data">
                            <div v-if="GSBH === false" class="action">
                                <button @click="showEditFormAdminProgram(data.item)" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <button @click="showDeleteFormAdminProgram(data.item.id)" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                               
                            </div>
                            <div v-else>
                                <span class="badge badge-danger">Bạn không có quyền</span>
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
        <FormAdminProgram ref="formAdminProgram" :fetchProgramAdmin="fetchProgramAdmin" @fetchProgramAdmin="getPrograms">
        </FormAdminProgram>
    </div>
</template>
<script>
import FormAdminProgram from './FormAdminProgram.vue';
import Treeselect from '@riophae/vue-treeselect';
import '@riophae/vue-treeselect/dist/vue-treeselect.css';
export default {
    components: {
        FormAdminProgram,
        Treeselect
    },
    data() {
        return {
            token: '',
            loading: false,
            GSBH: false,
            show_search: false,

            filter: {
                program_id: null,
                query: '',
            },

            fields: [
                {
                    key: 'index',
                    label: 'STT',
                    sortable: true,
                    class: 'text-nowrap',

                },
                {
                    key: 'program_id',
                    label: 'Chương trình',
                    sortable: true,
                    class: 'text-nowrap',

                },
                {
                    key: 'user_id',
                    label: 'Người quản lý',
                    sortable: true,
                    class: 'text-nowrap',

                },
                {
                    key: 'actions',
                    label: 'Hành động',
                    sortable: true,
                    class: 'text-nowrap text-center',
                },
            ],

            total: 0,
            per_page: 10,
            from: 1,
            to: 0,
            current_page: 1,
            pageOptions: [10, 50, 100, 500, { value: this.rows, text: "All" }],

            programs: [],

            program_admins: [],
            page_url_admin_program: '/api/program_admin',
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchProgramAdmin();
    },
    methods: {
        fetchProgramAdmin() {
            this.loading = true;
            const params = new URLSearchParams({
                program_id: this.filter.program_id,

            });
            var page_url = this.page_url_admin_program + '?' + params.toString();
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {
                    this.program_admins = data.data;
                    this.GSBH = data.GSBH;
                    this.loading = false;

                })
                .catch(err => {
                    console.log(err);
                    this.loading = false;

                });
        },
        showFormAdminProgram() {
            this.$refs.formAdminProgram.showModal();
        },
        showEditFormAdminProgram(program_admin) {
            this.$refs.formAdminProgram.editProgramAdmin(program_admin);
        },
        showDeleteFormAdminProgram(id) {
            this.$refs.formAdminProgram.deleteProgramAdmin(id);
        },
        getPrograms(programs) {
            this.programs = programs;
        },
        showSearch() {
            this.show_search = !this.show_search;
        }
    },
    computed: {
        rows() {
            return this.program_admins.length;
        },
    },

}
</script>

<style lang="scss" scoped>
.custom-container {
    overflow-x: auto;
}

.event-edit {
    cursor: pointer;
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
    background-color: #04da2b;
    border-radius: 10px;
    animation: expandAnimation 0.5s linear;
}</style>