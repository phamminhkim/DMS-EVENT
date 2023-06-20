<template>
    <div>
        <div class="modal fade" id="tess_modal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase">Dannh sách chương trình tham gia</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="border-bottom">
                            <h6 class="font-weight-bold">Thông tin nhân viên</h6>
                            </div>
                            <p class="mt-3">
                                <label class="text-secondary">Mã nhân viên: <span class="font-weight-bold">{{ current_user.staff_code }}</span> </label><br>
                                <label class="text-secondary">Tên: <span class="font-weight-bold"> {{ current_user.name }}</span></label><br>
                            </p>
                            <label class="font-weight-bold text-uppercase"> Tham gia vào các chương trình sau: </label>
                            <div class="row">
                                <div class="col-lg-4" v-for="(join_program, index) in show_join_programs" :key="index">
                                    <div class="card shadow mb-3" style="height:100%">
                                        <div class="card-header bg-warning" style="height:100%">
                                            <div v-if="join_program.program_id" class="d-flex">
                                                <div>
                                                    <span class="font-weight-bold"> {{ index + 1 }}. </span>
                                                </div>
                                                <div class="ml-2">
                                                    <span class="font-weight-bold">
                                                        {{ join_program.program.name }} 
                                                    </span> <br>
                                                    <small>Mã chương trình: <span> {{ join_program.program.dms_code }} </span> </small><br>
                                                    <small>Ngày bắt đầu: <span> {{ join_program.program.start_date }} </span> </small><br>
                                                    <small>Ngày kết thúc: <span> {{ join_program.program.end_date }} </span> </small><br>
                                                    <small v-if="join_program.user_id">Giám sát chương trình: {{ join_program.user.staff_code }} - {{ join_program.user.name }} </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body" style="height:100%">
                                            <div class="form-group"
                                                v-for="(program_stage, index_stage) in join_program.program_stages">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="form-group mb-0">
                                                            <div>
                                                                <span class="text-opacity">
                                                                    <i class="far fa-bookmark" style="width:13px"></i> Đợt: {{
                                                                        program_stage.stage }}
                                                                    <span v-if="program_stage.status == 0"
                                                                        class="text-danger font-weight-bold badge">Đang đóng</span>
                                                                    <span v-if="program_stage.status == 1"
                                                                        class="text-success font-weight-bold badge">Đang mở</span>
                                                                    </span>
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    </div>
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
           current_user: window.Laravel.current_user,

            fields: [
                {
                    key: 'id',
                    label: 'STT',
                    sortable: true,
                   class: 'text-nowrap'
                },
                {
                    key: 'program.dms_code',
                    label: 'Mã chương trình',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'program_id',
                    label: 'Tên chương trình',
                    sortable: true,
                    class: 'text-nowrap'
                },
                {
                    key: 'stage_status',
                    label: 'Trạng thái',
                    sortable: true,
                    thClass: 'text-center',
                    tdClass: 'text-center',
                    class: 'text-nowrap'
                },
                {
                    key: 'user_id',
                    label: 'Giám sát bán hàng',
                    thClass: 'text-center',
                    tdClass: 'text-center',
                    class: 'text-nowrap'
                },
            ],

            show_join_programs: [],
            page_url_show_join_program: '/api/show-join-program',
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
    },
    methods: {
        fetchJoinProgram() {
            this.loading = true;
            var page_url = this.page_url_show_join_program;
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {
                    this.show_join_programs = data.data;
                    this.loading = false;
                })
                .catch(err => {
                    console.log(err);
                    this.loading = false;
                });
        },
        openModal() {
            this.fetchJoinProgram();
            $('#tess_modal').modal('show');
        }
    }
}
</script>

<style lang="scss" scoped>
.form-header{
    letter-spacing: 2px;
   
}
.text-opacity {
    color: rgb(176, 176, 176);
}
</style>