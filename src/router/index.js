import {createRouter, createWebHistory} from 'vue-router';
import { useAuthStore } from '@stores';

import PublicLayout from "@layouts/PublicLayout.vue";
import AdminLayout from "@layouts/AdminLayout.vue";

const publicRoutes = [
    {
        path: '/login',
        name: 'Login',
        component: () => import('../pages/Login.vue')
    },
    {
        path: '/',
        name: 'Index',
        component: () => import('../pages/public/Index.vue')
    },
];

const adminRoutes = [
    {
        path: '/admin/companies',
        name: 'AdminCompanies',
        component: () => import('../pages/admin/Companies.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true
        }
    },
    {
        path: '/admin/users',
        name: 'AdminUsers',
        component: () => import('../pages/admin/Users.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true
        }
    },
];

const routes = [...publicRoutes, ...adminRoutes,
    {
        path: '/:pathMatch(.*)*',
        name: '404',
        component: () => import('../pages/404.vue')
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const auth = useAuthStore();

    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!auth.user) {
            next({
                path: '/login',
                //query: { redirect: to.fullPath }
            });
        } else {
            next();
        }
    } else {
        next();
    }
})

export default router;