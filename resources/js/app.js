require('./bootstrap');
require('../sass/app.scss')
import Vue from 'vue'
import VueRouter from 'vue-router';
    
Vue.use(VueRouter);
window.Vue = require('vue');

// router
import router from './routes.js';
window.router = router;
window.Fire = new Vue();

const app = new Vue({
    el: '#app',
    router,
}).$mount('#app');