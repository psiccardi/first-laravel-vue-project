import './bootstrap';
// import { BootstrapVue, IconsPlugin } from 'bootstrap-vue-next'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css'
import BootstrapVueNext from 'bootstrap-vue-next'
import VueSidebarMenu from 'vue-sidebar-menu'
import 'vue-sidebar-menu/dist/vue-sidebar-menu.css'
import { createApp } from "vue";
import i18n from './i18n/index';
import Backoffice from "../src/Backoffice.vue";
import router from '../src/backoffice/router';

import './../css/app.css';
import './../css/backoffice.css';
i18n.global.locale.value = 'en'
const app = createApp(Backoffice)
app
    .use(BootstrapVueNext)
    .use(i18n)
    .use(router)
    .mount("#backoffice")
