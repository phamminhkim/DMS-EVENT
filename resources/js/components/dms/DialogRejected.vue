<template>
    <div>
        <div class="modal fade" id="dialog_rejected" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form @submit.prevent="rejectedSubmission">
                        <div class="modal-header border-0 py-1">
                            <h5 class="modal-title text-danger">Từ chối</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input v-model="submission.feedback_content" class="form-control" placeholder="Lý do......" />
                        </div>
                        <div class="modal-footer d-flex justify-content-between border-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Xác nhận</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        fetchSubmisstion: Function,
        component: String,
    },
    data() {
        return {
            token: '',
            loading: false,
            errors: {},

            submission: {
                id: '',
                feedback_content: '',
            },
            page_url_submission_is_rejected: '/api/submission-is_rejected',
            page_url_submission_is_rejected_level2: '/api/submission-is_rejected_level2',
        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
    },
    methods: {
        show(submission) {
            this.submission.id = submission.id;
            this.submission.feedback_content = submission.feedback_content;
            $('#dialog_rejected').modal('show');
        },
        rejectedSubmission() {
            if (this.component == 'approved') {
                var page_url = this.page_url_submission_is_rejected + '/' + this.submission.id;
                fetch(page_url, {
                    method: "PUT",
                    body: JSON.stringify(this.submission),
                    headers: {
                        "content-type": "application/json",
                        Authorization: this.token
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data.data.success);
                        if (data.data.success == 1) {
                            this.showMessage('success', 'Từ chối', 'Từ chối thành công');
                            this.$props.fetchSubmisstion();
                            $('#dialog_rejected').modal('hide');
                            this.reset();
                        } else {
                            this.errors = data.data.errors;
                        }

                    })
                    .catch(err => {
                        console.log(err);
                    });
            } else {
                var page_url = this.page_url_submission_is_rejected_level2 + '/' + this.submission.id;
                fetch(page_url, {
                    method: "PUT",
                    body: JSON.stringify(this.submission),
                    headers: {
                        "content-type": "application/json",
                        Authorization: this.token
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log(data.data.success);
                        if (data.data.success == 1) {
                            this.showMessage('success', 'Từ chối', 'Từ chối thành công');
                            this.$props.fetchSubmisstion();
                            $('#dialog_rejected').modal('hide');
                            this.reset();
                        } else {
                            this.errors = data.data.errors;
                        }

                    })
                    .catch(err => {
                        console.log(err);
                    });
            }

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
        reset() {
            this.submission.id = '';
            this.submission.feedback_content = '';
        }
    }
}
</script>