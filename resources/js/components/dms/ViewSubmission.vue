<template>
    <div class="modal fade" id="view_submission" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-if="view_subbmission.submission.program_id"> {{ view_subbmission.submission.program.name }} </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="text-sm mt-2">
                            <i class="far fa-id-card text-secondary"></i> Khách hàng:
                            <span v-if="view_subbmission.submission.customer_id" class="font-weight-bold content">
                                {{ view_subbmission.submission.customer.name }}
                            </span>
                        </label>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4 mt-2 image" v-for="(image, index) in view_subbmission.files"
                                :key="index">
                                <div class="img-ralative">
                                  
                                    <img  @click.prevent="showImageDetail(image.url)" :src="'/' + image.url"
                                        class="w-100 img-responsive mt-2" alt="Ảnh đã chọn">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>ghi chú: {{ view_subbmission.submission.note }} </label>
                    </div>
                </div>
            </div>
        </div>
        <ImageDetail ref="imageDetail"></ImageDetail>
    </div>
</template>

<script>
import ImageDetail from './ImageDetail.vue';

export default {
    components: {
        ImageDetail
    },
    data() {
        return {
            view_subbmission: {
                submission: {},
                files: [],
            },
        }
    },
    methods: {
      
        viewSubmission(submission) {
            this.view_subbmission = submission;
            $('#view_submission').modal('show');
        },
        showImageDetail(image_path) {
            this.$refs.imageDetail.viewImageDetail(image_path);
        }
    }
}
</script>

<style lang="scss" scoped>
.img-ralative {
    position: relative;
    width: 100%;
    height: 100%;

}

.img-responsive {
    max-width: 100%;
    object-fit: cover;
    height: 100%;

    &:hover {
        cursor: pointer;
        transform: scale(1.1);
        transition: all 0.5s ease;
    }
}</style>