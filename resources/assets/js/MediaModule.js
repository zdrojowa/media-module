import Vue from 'vue'
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'
import VueRouter from 'vue-router'
import Index from './components/Index'

window.axios = require('axios');

Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
Vue.use(VueRouter)
Vue.use(require('vue-moment'))

const app = document.getElementById('app');

new Vue({
    components: {Index},

    props: ['route'],

    data() {
        return {
            search: '',
            files: []
        }
    },

    mounted() {
        this.find()
    },

    methods: {
        find() {
            let self = this
            axios.get('/api/media/files?search=' + self.search)
            .then(function(response) {
                self.files = response.data
            }).catch(function(error) {
                console.log(error)
            })
        },

        upload(file) {
            this.files.unshift(file)
        },

        remove(i) {
            this.files.splice(i, 1)
        }
    }
}).$mount(app);