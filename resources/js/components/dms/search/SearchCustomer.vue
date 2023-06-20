<template>
    <div>
        <div class="form-goup form-search">
            <input class="form-control" placeholder="Tìm kiếm khách hàng..." v-model="searchKeyword"
            @keyup.down="navigateDown()" @keyup.up="navigateUp()"
            @keyup.enter="selectItem(submissions[selectedIndex])" />
            <div class="card item-search">
                <div style="overflow-y:scroll;height:500px">
                  
                    <div class="text-center" v-if="loading">
                        <i class="fad fa-spinner fa-pulse "
                            style="--fa-primary-color: #3277d2; --fa-primary-opacity: 0.3; --fa-secondary-color: #2a3ea2; --fa-secondary-opacity: 0.8;font-size:30px"></i>
                        <br>
                        <span class="text-secondary font-weight-bold text-xs font-italic">Vui lòng chờ giây
                            lát...</span>
                    </div>
                    <span class="mt-1 p-2 font-italic text-secondary">Tìm thấy {{ submissions.length }}/{{
                        paginate_customer.total }} kết quả</span>
                    <div class="form-group query" v-for="(query, index) in submissions" :key="index" @click="getValue(query)"
                        :class="{ 'selected': index === selectedIndex }">
                        <div class="mt-1">
                            <span class="text-secondary">
                                <span class="font-weight-bold">{{ index + 1 }} </span> .
                                <span v-html="highlightMatched(query.name)"></span>
                                <span v-if="query.dms_code" class="float-right">
                                    <span v-html="highlightMatched(query.dms_code)"></span>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>

    </div>
</template>
<script>
export default {
    props: {
        fetchSubmissionStageImage: Function,
    },  
    data() {
        return {
            token: '',
            loading: false,
            submissions: [],
            selectedIndex: -1,
            searchKeyword: '',
            errors: {},

            filter: {
                customer_id: '',
            },

            paginate_customer: {
                current_page: 1,
                last_page: 1,
                total: 0,
            },
          

            page_url_submission: '/api/customer-page',
        }
    },
    watch: {
        searchKeyword: function (newVal, oldVal) {
            clearTimeout(this.typingTimer);
            this.typingTimer = setTimeout(() => {
                this.fetchSubmisstion();
                if(this.searchKeyword == ''){
                    this.filter.customer_id = '';
                    this.$emit('SearchCustomer', this.filter);
                }
            }, 500);

        }
    },
    created() {
        this.token = "Bearer " + window.Laravel.access_token;
        this.fetchSubmisstion();

    },
    methods: {
        fetchSubmisstion() {
            this.loading = true;
            this.errors = {};
            const queryParams = new URLSearchParams({
                query: this.searchKeyword,
            });
            var page_url = this.page_url_submission + '?' + queryParams.toString();
            fetch(page_url, {
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.token
                }
            })
                .then(res => res.json())
                .then(data => {
                    this.submissions = data.data;
                    this.paginate_customer = data.paginate;
                    this.selectedIndex = -1;
                    this.loading = false;
                })
                .catch(err => {
                    console.log(err);
                    this.loading = false;

                });
        },
        getValue(item) {
            this.filter.customer_id = item.id;
            this.searchKeyword = item.name;
            this.$emit('SearchCustomer', this.filter);
           
        },
        navigateDown() {
            if (this.selectedIndex < this.submissions.length - 1) {
                this.selectedIndex++;
            }
        },
        navigateUp() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
            }
        },
        selectItem(query) {
            this.getValue(query);
        },
        highlightMatched(str) {
            const regex = new RegExp(this.searchKeyword, 'gi');
            return str.replace(regex, match => `<span class="text-black font-weight-bold">${match}</span>`)
        },
        reset() {
            this.searchKeyword = '';
            this.filter.customer_id = '';
        }
    }
}
</script>


<style lang="scss" scoped>
.form-search {
    position: relative;
}

.item-search {
    position: absolute;
    width: 100%;
    z-index:11;
    visibility: hidden;
    opacity: 0;
    transition: opacity .3s ease-out, visibility .3s ease-out;

    &::before {
        position: absolute;
        content: '';
        width: 100%;
        background-color: red;
    }
}


.query {
    padding: 5px;

    &:hover {
        border-left: 3px solid darkblue;
        background: rgb(242, 242, 242);
        cursor: pointer;
    }

}


input:focus+.item-search {
    visibility: visible;
    opacity: 1;
}

.search-item {
    color: gray;
    font-weight: 700;

    &:hover {
        border: 1px solid gray;
        color: rgb(0, 140, 255);
    }

}

.position-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.selected {
    background-color: #e4e4e4;
}




</style>