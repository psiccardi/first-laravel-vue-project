import './bootstrap';
// import { BootstrapVue, IconsPlugin } from 'bootstrap-vue-next'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css'
import BootstrapVueNext from 'bootstrap-vue-next'
import { createApp } from "vue";
import Login from "../src/Login.vue";
import i18n from './i18n/index';

import './../css/app.css';
import './../css/login.css';
i18n.global.locale.value = 'en'
const app = createApp(Login)
app.use(BootstrapVueNext);
app.use(i18n);
app.mount("#login")
// app.use(IconsPlugin);
