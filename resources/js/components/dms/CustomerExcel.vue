<template>
    <div>
        <div class="modal fade" id="customer_excel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-import-excel ">
                            <input type="file" ref="fileInput" @change="handleFileChange" />
                            <p class="mx-sm-auto" v-if="load !== ''" style="position:relative"><i
                                    class="fas fa-file-contract mt-4" style="font-size:30px"></i><br>File:
                                {{ load }}
                                <span class="text-danger"
                                    style="position:absolute;top:0;right:10px;font-size:25px;cursor:pointer;"
                                    @click="clearFileInput()"><i class="fas fa-times-circle"></i></span>
                            </p>
                            <p class="mx-sm-auto" v-else><i class="fas fa-file-contract mt-4" style="font-size:30px"></i><br>
                                Thêm file
                                Excel <br><span class="text-secondary" style="font-size:10px">hoặc kéo và thả</span></p>
                            <div class="text-right update_data_excel" style="position:absolute;top:5px;right:5px;">
                                <button v-if="load !== ''" class="btn btn-outline-info btn-xs" @click="showDataExcel()"><i
                                        class="fas fa-eye"></i> Xem dữ liệu Excel</button> <br>
                                <button v-if="load !== ''" class="btn btn-outline-warning btn-xs" @click="chooseNewFile()"><i
                                        class="fas fa-edit"></i> Thay đổi file</button>
                            </div>
                        </div>
                        <div class="text-center mt-1">
                            <button v-if="load !== ''" type="button" class="btn btn-success" @click="uploadFiles()"><i
                                    class="fas fa-upload"></i> Upload</button>
                        </div>
                        <div class="form-group table-responsive">
                            <label>Trường mẫu File Excel: </label>
                            <a class="float-right" style="cursor:pointer" @click="exportExcel"> <i class="fas fa-download"></i>
                                <u>Dowload templates Excel</u> </a>
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr class="font-weight-bold">
                                        <td class="px-md-1" v-for="field_excel in selected_fields">{{
                                            field_excel }}</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-secondary font-italic">
                                        <td v-for="data_excel in selectedExcel"> {{ data_excel.value }} <span
                                                class="text-danger">
                                                ({{ data_excel.label }}) </span> </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                       
                    </div>
                </div>
            </div>
        </div>
        <div class="modal " tabindex="-1" role="dialog" id="data_excel">
            <div class="modal-dialog fullscreen" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xem chi tiết</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="loading" class="text-center">
                            <i class="far fa-hourglass fa-spin" style="font-size:30px" ></i>
                            <h6 class="loading-text">Đang tải</h6>
                        </div>
                        
                        <label>Danh sách Excel đã thêm vào: <i class="fas fa-sort-amount-down text-success"></i></label>
                        <div v-if="load !== ''" type="button" class="btn bg-gradient-success float-right css-upload-excel"
                            style="position:sticky;top:10px;z-index:1;border-radius: 30px;" @click="uploadFiles()">
                            <i class="fas fa-upload"> Upload</i>
                        </div>
                        <div v-if="!loading" class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr class="font-weight-bold">
                                        <th>#</th>
                                        <th v-for="column in columns">{{ column }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, rowIndex) in rowss" :key="rowIndex">
                                        <td class="font-weight-bold text-secondary">{{ rowIndex + 1 }}</td>
                                        <td v-for="(cell, cellIndex) in row" :key="cellIndex">{{ cell }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
   
</template>

<script>
import * as XLSX from 'xlsx';

import axios from 'axios';
export default {
    props: {
        fetchCustomer: Function,
    },
    data() {
        return {
            load: '',
            error_import_excel: {},
            selected_fields: ["Mã nhân viên bán hàng","DMS Code", "Tên khách hàng", "Địa chỉ"], // thuộc tính được chọn
            data: [
                { label: "Mã nhân viên bán hàng", value: "" },
                { label: "DMS Code", value: "" },
                { label: "Tên khách hàng", value: "" },
                { label: "Địa chỉ", value: "" },
            ],
            token:'',
            columns: [],
            rowss: [],
            properties: [],
            tableData: null,
            loading: false,
            tableHeader: null,
            error_import_excel: {},
            failed: {},
            page_url_import_customer: "/api/customer-import-excel", 
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        $("#spinner-opts small").css({
            display: "inline-block",
            width: "60px",
        });
    },
    methods: {
        exportExcel() {
            const selectedFieldsArray = Array.isArray(this.selected_fields) ? this.selected_fields : [this.selected_fields];
            const headers = [...selectedFieldsArray];
            const data = this.data.map(obj => headers.map(header => obj[header])) // lấy dữ liệu theo thuộc tính được chọn
            const worksheet = XLSX.utils.aoa_to_sheet([headers, ...data]) // tạo worksheet từ dữ liệu và header
            const workbook = XLSX.utils.book_new() // tạo workbook mới
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1') // thêm worksheet vào workbook
            XLSX.writeFile(workbook, 'data.xlsx') // tạo file excel và tải xuống
        },
        handleFileChange(event) {
            this.load = event.target.files[0].name;
            const file = this.$refs.fileInput.files[0];
            const reader = new FileReader();

            reader.onload = (e) => {
                const data = e.target.result;
                const workbook = XLSX.read(data, { type: 'binary' });
                const sheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[sheetName];
                const rows = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                if (rows.length > 0) {
                    this.columns = rows[0];
                    this.rowss = rows.slice(1);
                }
            };

            reader.readAsBinaryString(file);
            $("#data_excel").modal("show");
        },
        uploadFiles() {
            this.loading = true;
            const file = this.$refs.fileInput.files[0];
            var page_url = this.page_url_import_customer;
            const formData = new FormData();
            formData.append('file', file);
            axios.post(page_url, formData, {
                headers: {
                    'Authorization': this.token,
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(response => {
                    this.failed = JSON.stringify(response.data);
                    const failedData = JSON.parse(this.failed);
                    if (failedData.success == 0) {
                        this.showMessage('warning', failedData.message);
                        this.loading = false;
                    } else {
                        this.showMessage('success','Import File Excel thành công');
                        this.tableData = response.data.data;
                        this.loading = false;
                        $('#customer_excel').modal('hide');
                        $("#data_excel").modal("hide");
                        this.clearFileInput();
                        this.$props.fetchCustomer();
                    }
                })
                .catch(error => {
                    console.error(error);
                    if (error.response) {
                        // Server trả về mã lỗi HTTP và thông báo lỗi
                        this.error_import_excel = error.response.data.message;
                        this.showMessage('warning',this.error_import_excel);
                        this.loading = false;
                    } else {
                        // Lỗi xảy ra khi gửi yêu cầu AJAX
                        console.log(error.message);
                        this.loading = false;
                    }
                });
        },
        clearFileInput() {
            this.load = '';
            const fileInput = this.$refs.fileInput;
            fileInput.type = 'text';
            fileInput.type = 'file';
        },
        showModal() {
            $('#customer_excel').modal('show');
        },
        showDataExcel() {
            this.loading = false;
            $("#data_excel").modal("show");
        },
        chooseNewFile() {
            this.clearFileInput();
            this.$refs.fileInput.click();
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
    computed: {
        selectedExcel() {
            return this.selected_fields.map(function (field) {
                return { label: field, value: "Nhập" };
            });
        }
    },
}
</script>

<style lang="scss" scoped>
.form-import-excel {
    position: relative;
    width: 100%;
    height: 150px;
    background: rgb(247, 248, 250);

}

.form-import-excel p {
    width: 50%;
    height: 100%;
    text-align: center;
    color: black;

    border-radius: 10px;
    border: 3px dotted lightgray;

}

.form-import-excel:hover p {
    background: rgb(234, 235, 237);

}

.form-import-excel input {
    position: absolute;
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    outline: none;
    opacity: 0;
    cursor: pointer;
}
.fullscreen {
    width: 100% !important;
    max-width: none !important;
    height: 100% !important;
    margin: 0 !important;
}
.css-upload-excel{
    background: rgb(18, 162, 109);
    color:white;
    &:hover{
        background: rgb(21, 208, 140);
        color:white;
    }
}
.animation-upload{
    background: green;
}
.animate-charcter
{
   text-transform: uppercase;
  background-image: linear-gradient(
    -225deg,
    #231557 0%,
    #44107a 29%,
    #ff1361 67%,
    #fff800 100%
  );
  background-size: auto auto;
  background-clip: border-box;
  background-size: 200% auto;
  color: #fff;
  background-clip: text;
  text-fill-color: transparent;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: textclip 2s linear infinite;
  display: inline-block;
      font-size: 190px;
}

@keyframes textclip {
  to {
    background-position: 200% center;
  }
}
@keyframes loading {
    0% { content: ""; }
    25% { content: "."; }
    50% { content: ".."; }
    75% { content: "..."; }
    100% { content: ""; }
  }
  
  .loading-text::after {
    content: "";
    animation: loading 1s infinite;
  }
</style>