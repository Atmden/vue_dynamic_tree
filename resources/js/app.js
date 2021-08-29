import './bootstrap';
window.Vue = require('vue');
Vue.component('dyntreecomponent', require('./components/DynTreeComponent.vue').default);

const app = new Vue({
    el: '#app',
    data: {
    }
});
