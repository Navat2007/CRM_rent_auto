import {createRouter, createWebHistory} from 'vue-router';
import { useAuthStore } from '@stores';

import PublicLayout from "@layouts/PublicLayout.vue";
import AdminLayout from "@layouts/AdminLayout.vue";

import NotFound from "@pages/404.vue";

const publicRoutes = [
    {
        path: '/login',
        name: 'Login',
        component: import('@pages/Login.vue')
    },
    {
        path: '/',
        name: 'Index',
        component: import('@pages/public/Index.vue')
    },
];

const adminRoutes = [
    {
        path: '/admin',
        name: 'Admin',
        component: import('@pages/admin/Admin.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
        }
    },
    // Employers
    {
        path: '/admin/employers',
        component: import('@pages/admin/employers/Employers.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Сотрудники',
        }
    },
    {
        path: '/admin/employers/new',
        component: import('@pages/admin/employers/AddEmployer.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новый сотрудник',
        }
    },
    {
        path: '/admin/employers/:id',
        component: import('@pages/admin/employers/EditEmployer.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Редактирование сотрудника',
        }
    },
    // Directory
    {
        path: '/admin/directory/positions',
        component: import('@pages/admin/directory/position/Positions.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Должности',
        }
    },
    {
        path: '/admin/directory/advertising_types',
        component: import('@pages/admin/directory/advertising_types/AdvertisingTypes.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Виды рекламы',
        }
    },
];

const routes = [...publicRoutes, ...adminRoutes,
    {
        path: '/:catchAll(.*)',
        name: '404',
        component: NotFound,
        meta: {
            title: 'Страница не найдена',
        }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


router.beforeEach((to, from, next) => {
    const auth = useAuthStore();
    const DEFAULT_TITLE = auth.user.company_name || 'АВТОПРОКАТ';

    document.title = to.meta.title || DEFAULT_TITLE;

    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!auth.user) {
            next({path: '/login'});
        } else {
            next();
        }
    } else {
        next();
    }
})

export default router;