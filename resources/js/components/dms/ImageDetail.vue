<template>
    <div class="modal fade" id="image_detail" tabindex="-1" style="background:black">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="text-center" style="position:relative;width:100%;height:500px;">
                        <div class="img-relative">
                            <video v-if="isVideo(image_detail.image_path)" class="w-100 video" controls style="height:100%">
                                <source :src="image_detail.image_path" type="video/mp4">
                            </video>
                            <img v-else :src=" image_detail.image_path" class="img-responsive w-100" />
                        </div>
                       
                    </div>
                    <button @click.prevent="closeImageDetail()" type="button" class="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
           
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            image_detail: {
                image_path: '',
                note: '',
            }
        }
    },
    mounted() {

        $("#image_detail").on("hidden.bs.modal", () => {
            this.image_detail.image_path = '';
        });
    },
    methods: {
        isVideo(path) {
            const videoExtensions = ['.mp4', '.avi', '.mov', '.mkv'];
            const extension = path.substring(path.lastIndexOf('.')).toLowerCase();
            return videoExtensions.includes(extension);
        },
        viewImageDetail(image_path) {
            this.image_detail.image_path = image_path;
            $('#image_detail').modal('show');
        },
        closeImageDetail() {
            $('#image_detail').modal('hide');
        }
    }
}
</script>

<style lang="scss" scoped>
.full_modal-dialog {
    margin: 0 !important;
    width: 100% !important;
    height: 100% !important;
    min-width: 100% !important;
    min-height: 100% !important;
    max-width: 100% !important;
    max-height: 100% !important;
    padding: 0 !important;
}

.close {
    cursor: pointer;
}
.video{
    height: 100%;
    object-fit: contain;
}
.img-relative{
    position: relative;
    width: 100%;
    height: 100%;
}
.img-responsive{
    max-width: 100%;
    object-fit: contain;
    height: 100%;
    border-radius: 5px;
}
</style>