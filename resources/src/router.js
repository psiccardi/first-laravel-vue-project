import { createRouter, createWebHistory } from "vue-router";
import { getUserAPI } from "../js/utilities/api";

/**
 * First level components
 */
// Index (main component)
import Index from './Index.vue'

// 404 page
import NotFound from './NotFound.vue';

/**
 * Index sub-components
 */
//Backoffice (main backoffice component)
import Backoffice from './pages/Backoffice.vue'

//Blog
import Blog from './pages/Blog.vue';

//Login page
import Login from './pages/Login.vue';

/**
 * Backoffice sub-components
 */
// Backoffice Profile page
import BackofficeProfile from './pages/backoffice/Profile.vue'

// Backoffice Users page (restricted to 'admin' role)
import BackofficeAdminUsers from './pages/backoffice/admin/Users.vue'

// Backoffice Posts page
import BackofficePosts from './pages/backoffice/Posts.vue'
import { ref, defineEmits } from "vue";
import Utils from "./../js/utilities/utils.js";
import { useI18n } from "vue-i18n";
const routes = [
    {
        path: '/',
        component: Index,
        name: 'Index',
        children: [
            {
                path: '',
                component: Blog,
                name: 'Blog'
            },
            {
                path: 'login',
                component: Login,
                name: 'Login'
            },
            {
                path: 'backoffice',
                component: Backoffice,
                name: 'Backoffice',
                children: [
                    {
                        path: 'profile',
                        component: BackofficeProfile,
                        name: 'Profile'
                    },
                    {
                        path: 'admin/users',
                        component: BackofficeAdminUsers,
                        meta: {
                            role: ['administrator']
                        },
                        name: 'AdminUsers'
                    },
                    {
                        path: 'admin/posts',
                        component: BackofficePosts,
                        meta: {
                            role: ['administrator']
                        },
                        name: 'AdminPosts'
                    },
                    {
                        path: 'operator/posts',
                        component: BackofficePosts,
                        name: 'OperatorPosts'
                    },
                ]
            }
        ]
    },
    {
        path: "/:catchAll(.*)",
        component: NotFound,
        name: 'NotFound'
    }
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
