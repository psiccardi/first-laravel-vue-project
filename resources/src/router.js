import { createRouter, createWebHistory } from "vue-router";
import { getUserAPI } from "../js/utilities/api";

import main from './Index.vue'
import sectionBackoffice from './Backoffice.vue'
import sectionLogin from './Login.vue';
import pageProfile from './backoffice/pages/Profile.vue'

import pageUsers from './backoffice/pages/Users.vue'
import pagePosts from './backoffice/pages/Posts.vue'
import { ref, defineEmits } from "vue";
import Utils from "./../js/utilities/utils.js";
import { useI18n } from "vue-i18n";
const routes = [
    {
        path: '/',
        component: main,
        name: 'Index',
        children: [
            {
                path: 'login',
                component: sectionLogin,
                name: 'Login'
            },
            {
                path: 'backoffice',
                component: sectionBackoffice,
                name: 'Backoffice',
                children: [
                    {
                        path: 'profile',
                        component: pageProfile,
                        name: 'Profile'
                    },
                    {
                        path: 'admin/users',
                        component: pageUsers,
                        meta: {
                            role: ['administrator']
                        },
                        name: 'AdminUsers'
                    },
                    {
                        path: 'admin/posts',
                        component: pagePosts,
                        meta: {
                            role: ['administrator']
                        },
                        name: 'AdminPosts'
                    },
                    {
                        path: 'operator/posts',
                        component: pagePosts,
                        name: 'OperatorPosts'
                    },
                ]
            }
        ]
    },
    // {
    //     path: '/:pathMatch(.*)*',
    //     component: notFound
    // }
]

const router = createRouter({

    history: createWebHistory(),

    routes

})

const checkUser = async function (fn) {
    return new Promise(function (resolve, reject) {
        getUserAPI(
            {},
            (resp) => {
                Utils.response.handleError(resp);
                resolve(resp);
            },
            (err) => {
                console.log(err.stack);
                resolve(null)
            }
        );
    })
};

router.beforeEach(async (to, from) => {
    const user = await checkUser()
    if (!to.meta) {
        return true;
    }
    if (to.meta.role && to.meta.role.indexOf(user?.role?.name) === -1) {
        return false;
    }
    return true;
})

export default router
