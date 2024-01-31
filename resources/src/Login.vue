<script setup>
    import './../css/login.css';

    import { ref } from 'vue';
    import Locale from './components/Locale.vue'
    import { setCookie } from './../js/utilities/cookies';
    import { useI18n } from 'vue-i18n';
    import { jsonPostAPI, jsonGetAPI, getAPI, loginWebAPI } from '../js/utilities/api.js';
    const { t, locale } = useI18n();
    import Utils from './../js/utilities/utils.js';
    const email = ref('');
    const password = ref('');
    const csrf = ref(document.querySelector('meta[name="csrf-token"]').getAttribute('content'))
    // const emailText = t('login.email');
    // const passwordText = t('login.password');
    function login() {
        getAPI('/sanctum/csrf-cookie', {}, resp => {
            loginWebAPI({
                email: email.value,
                password: password.value,
                _token: csrf.value
            }, resp => {
                Utils.response.handleError(resp, t);
                if (resp.token) {
                    setCookie('auth_token', resp.token);
                    setTimeout(() => window.location.href = APP_URL + '/backoffice/profile', 100);
                }
            }, err => {
                Utils.DOM.toast(err.message, "error", t);
                console.log(err.stack)
            })
        }, err => {
            Utils.DOM.toast(err.message, "error", t);
            console.log(err.stack)
        })

        // jsonPostAPI('/api/login', {
        //     email: email.value,
        //     password: password.value
        // }, resp => {

        // }, err => {

        // })
    }
</script>
<template>
    <div class="login-wrapper">
        <div class="login-bar">
            <h1>Vue Login</h1>
        </div>
        <div class="right-column">
            <div class="locale-bar">
                <div></div>
                <Locale />
            </div>
            <div class="form-container">
                <b-form @submit.prevent="login">
                    <input type="hidden" name="_token" :value="csrf">
                    <b-form-group
                        id="input-group-email"
                        label-for="email"
                        description=""
                    >
                        <label for="email">
                            {{ t('email') }}
                        </label>
                        <b-form-input id="email" class="form-control" v-model="email" type="email" placeholder="" />
                    </b-form-group>
                    <b-form-group
                        id="input-group-password"
                        label-for="password"
                        description=""
                    >
                        <label for="password">
                            {{ t('password') }}
                        </label>
                        <b-form-input id="password" class="form-control" v-model="password" type="password" />
                    </b-form-group>
                    <br>
                    <b-form-group
                    id="input-group-submit"
                    >
                        <b-button variant="primary" type="submit" id="submit" >Send</b-button>
                    </b-form-group>
                </b-form>
            </div>
        </div>
    </div>
</template>
 <style scoped>

</style>
