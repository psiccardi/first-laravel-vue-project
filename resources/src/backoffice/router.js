import { createRouter, createWebHistory } from "vue-router";
import { getUserAPI } from "../../js/utilities/api";

import pageProfile from './pages/Profile.vue'
import pageUsers from './pages/Users.vue'
import pagePosts from './pages/Posts.vue'
import { ref, defineEmits } from "vue";
import Utils from "../../js/utilities/utils";
import { useI18n } from "vue-i18n";
const routes = [
    {
        path: '/',
    },
    {
        path: '/backoffice/profile',
        component: pageProfile,
        name: 'Profile'
    },
    {
        path: '/backoffice/admin/users',
        component: pageUsers,
        meta: {
            role: ['administrator']
        },
        name: 'AdminUsers'
    },
    {
        path: '/backoffice/admin/posts',
        component: pagePosts,
        meta: {
            role: ['administrator']
        },
        name: 'AdminPosts'
    },
    {
        path: '/backoffice/operator/posts',
        component: pagePosts,
        name: 'OperatorPosts'
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
                reject(err)
            }
        );
    })
};

router.beforeEach(async (to, from) => {
    const user = await checkUser()
    if (!to.meta) {
        return true;
    }
    if (to.meta.role && to.meta.role.indexOf(user.role?.name) === -1) {
        return false;
    }
    return true;
})

export default router
