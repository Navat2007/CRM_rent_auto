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
    {
        path: '/admin/companies',
        name: 'AdminCompanies',
        component: import('@pages/admin/companies/Companies.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Компании',
        }
    },
    {
        path: '/admin/companies/new',
        name: 'AdminAddCompany',
        component: import('@pages/admin/companies/AddCompany.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новая компания',
        }
    },
    {
        path: '/admin/companies/:id',
        name: 'AdminEditCompany',
        component: import('@pages/admin/companies/EditCompany.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Редактирование компании',
        }
    },
    {
        path: '/admin/users',
        name: 'AdminUsers',
        component: import('@pages/admin/users/Users.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Пользователи',
        }
    },
    {
        path: '/admin/users/new',
        name: 'AdminAddUser',
        component: import('@pages/admin/users/AddUser.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новый пользователь',
        }
    },
    {
        path: '/admin/users/:id',
        name: 'AdminEditUser',
        component: import('@pages/admin/users/EditUser.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Редактирование пользователя',
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

const DEFAULT_TITLE = 'БИ КАРС АВТОПРОКАТ';
router.beforeEach((to, from, next) => {
    const auth = useAuthStore();

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