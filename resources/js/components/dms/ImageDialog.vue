<template>
    <div>
        <div class="modal" id="image_dialog" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-uppercase">ảnh trưng bày</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="carouse-ralative">
                            <div :id="'carouselExampleIndicators_' + id" class="carousel slide" data-ride="carousel"
                                data-interval="false">
                                <ol class="carousel-indicators">
                                    <li v-for="(image, index) in carousel_images" :key="index"
                                        :data-target="'#carouselExampleIndicators_' + id" :data-slide-to="index"
                                        :class="{ active: index === currentSlide }"></li>
                                </ol>
                                <div class="carousel-inner" style="position: relative; height: 500px;">
                                    <div v-for="(image, index) in carousel_images" :key="index"
                                        :class="{ 'carousel-item': true, 'active': index === currentSlide }"
                                        style="height:100%;background:black">

                                        <img :src="image.url" class="w-100" style="object-fit: contain;height: 100%;">
                                    </div>
                                </div>
                                <a class="carousel-control-prev" :href="'#carouselExampleIndicators_' + id" role="button"
                                    data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" :href="'#carouselExampleIndicators_' + id" role="button"
                                    data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>

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
            image_dialog: false,
            carousel_images: [],
            currentSlide: 0, // Chỉ số ảnh hiện tại
            id: '',
        }
    },
    mounted() {

        $("#image_dialog").on("hidden.bs.modal", () => {
            this.carousel_images = [];
            this.currentSlide = 0;
            this.id = '';
        });
    },
    methods: {
        showImageDialog(image, submission_id, index) {
            const selectedImage = image[index];
            image.splice(index, 1); // Xóa phần tử tại vị trí index
            image.unshift(selectedImage); // Thêm phần tử đã xóa vào đầu mảng
            this.carousel_images = [...image];
            this.id = submission_id;
            $('#image_dialog').modal('show');
        },
        prevSlide() {
            this.currentSlide = (this.currentSlide - 1 + this.carousel_images.length) % this.carousel_images.length;
        },
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.carousel_images.length;
        },
        isVideo(path) {
            const videoExtensions = ['.mp4', '.avi', '.mov', '.mkv'];
            const extension = path.substring(path.lastIndexOf('.')).toLowerCase();
            return videoExtensions.includes(extension);
        },
    }
}
</script>