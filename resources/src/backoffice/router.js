import {createRouter, createWebHistory } from "vue-router";

    import pageProfile from './pages/Profile.vue'
    import pageUsers from './pages/Users.vue'
    import pagePosts from './pages/admin/Posts.vue'

    const routes = [
        {
            path:   '/backoffice/profile',
            component: pageProfile
        },
        {
            path: '/backoffice/users',
            component: pageUsers
        },
        {
            path: '/backoffice/admin/posts',
            component: pagePosts
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

    export default router
