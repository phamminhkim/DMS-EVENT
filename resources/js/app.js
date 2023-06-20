
require('./bootstrap');

window.Vue = require('vue');
import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(BootstrapVue);
import moment from 'moment';
Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('DD/MM/YYYY')
    }
});


Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('dms-submission-register', require('./components/dms/SubmissionRegister.vue').default);
Vue.component('dms-submission-image', require('./components/dms/SubmissionImage.vue').default);
Vue.component('dms-submission-approved', require('./components/dms/Approved.vue').default);
Vue.component('dms-submission-customer', require('./components/dms/Customer.vue').default);
Vue.component('dms-program-admin', require('./components/dms/AdminProgram.vue').default);
Vue.component('dms-program', require('./components/dms/Program.vue').default);
Vue.component('dms-suppervision', require('./components/dms/Suppervision.vue').default);
Vue.component('dms-program-stages', require('./components/dms/ProgramStages.vue').default);
Vue.component('dms-lookup-customer', require('./components/dms/search/LookupCustomer.vue').default);



const app = new Vue({
    el: '#app',

    data() {
        return {



        }
    },
    ready() {

    },

});
Vue.config.productionTip = false;

