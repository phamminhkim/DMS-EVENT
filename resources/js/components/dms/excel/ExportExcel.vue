<template>
    <div>
        <div class="modal fade" id="excel" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold text-success text-uppercase">Trích Xuất Excel</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="download" class="alert alert-warning text-center fixed-aleft" role="alert">
                            <div class="d-flex align-items-end">
                                <strong>Đang tải dữ liệu</strong>
                                <div class="loading-dots">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-uppercase">Chọn Chương Trình</label>
                            <input v-model="searchText" class="form-control border-top-0 border-left-0 border-right-0"
                                placeholder="Tìm kiếm..." />
                            <div v-if="loading" class="text-center">
                                <i class="fas fa-circle spinner-grow"></i>
                            </div>
                            <div v-for="(program, index) in filteredPrograms" :key="index" class="p-2 mt-2"
                                style="background:rgb(154 255 153 / 40%);">
                                <span class=""> {{ index + 1 }}. </span>
                                <label class="font-weight-normal ">{{ program.name }}</label>
                                <input class="float-right form-control-sm" type="checkbox" :value="program.id"
                                    v-model="check.program_id" style="width:20px" />

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">

                        <button @click="exportExcel()" type="button"
                            class="float-right btn btn-success btn-sm p-2 rounded-pill px-4 font-weight-normal"><i
                                class="fas fa-file-export mr-2"></i>Trích xuất Excel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import * as XLSX from 'xlsx';
import axios from 'axios';
export default {
    data() {
        return {
            token: '',
            searchText: '',
            loading: false,
            download: false,
            programs: [],
            check: {
                program_id: [],
            },
            page_url_program: "/api/program",
            page_url_export_excel_submisstion: "/api/submissions-export"
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchProgram();
    },
    methods: {
        exportExcel() {
            this.download = true;
            const params = new URLSearchParams({
                program_id: this.check.program_id,

            });
            const pageUrl = this.page_url_export_excel_submisstion + '?' + params.toString();
            const headers = {
                'Authorization': this.token,

            };
            axios.get(pageUrl, { responseType: 'blob', headers })
                .then(response => {
                    const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'submissions.xlsx');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    this.download = false;
                })
                .catch(error => {
                    console.error(error);
                    this.download = false;
                });
        },
        fetchProgram() {
            this.loading = true;
            var page_url = this.page_url_program;
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {

                    this.programs = data.data;
                    this.loading = false;
                })
                .catch(err => {
                    console.log(err);
                    this.loading = false;

                });
        },
        showModal() {
            $('#excel').modal('show');
        },

    },
    computed: {
        filteredPrograms() {
            if (!this.searchText) {
                return this.programs;
            }
            return this.programs.filter(program => program.name.toLowerCase().includes(this.searchText.toLowerCase()));
        }
    }
}
</script>
<style lang="scss" scoped>
.fixed-aleft {
    position: fixed;
    bottom: -17px;
    left: 0;
}

.loading-dots {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 13px;
}

.loading-dots span {
    display: inline-block;
    width: 3px;
    height: 3px;
    background-color: #856404;;
    border-radius: 50%;
    margin: 0 1px;
    animation: jump 1.5s ease-in-out infinite;
}

.loading-dots span:nth-child(2) {
    animation-delay: 0.5s;
}

.loading-dots span:nth-child(3) {
    animation-delay: 1s;
}

@keyframes jump {
    0% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-2px);
    }

    100% {
        transform: translateY(0);
    }
}</style>