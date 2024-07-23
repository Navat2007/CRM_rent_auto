import {createRouter, createWebHistory} from 'vue-router';
import { useAuthStore } from '@stores';

import PublicLayout from "@layouts/PublicLayout.vue";
import AdminLayout from "@layouts/AdminLayout.vue";

// Public routes
import Index from "@pages/public/Index.vue";
import Login from "@pages/Login.vue";
import NotFound from "@pages/404.vue";

// Admin routes
import Admin from "@pages/admin/Admin.vue";
import AdminAddEmployer from "@pages/admin/employers/AddEmployer.vue";
import AdminEditEmployer from "@pages/admin/employers/EditEmployer.vue";
import AdminEmployers from "@pages/admin/employers/Employers.vue";
import AdminPositions from "@pages/admin/directory/position/Positions.vue";
import AdminAddPositions from "@pages/admin/directory/position/AddPositions.vue";
import AdminEditPositions from "@pages/admin/directory/position/EditPositions.vue";
import AdminAdvertisingTypes from "@pages/admin/directory/advertising_types/AdvertisingTypes.vue";
import AdminAddAdvertisingTypes from "@pages/admin/directory/advertising_types/AddAdvertisingTypes.vue";
import AdminEditAdvertisingTypes from "@pages/admin/directory/advertising_types/EditAdvertisingTypes.vue";

const publicRoutes = [
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/',
        name: 'Index',
        component: Index
    },
];

const adminRoutes = [
    {
        path: '/admin',
        name: 'Admin',
        exact: true,
        component: Admin,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
        }
    },
    // Employers
    {
        path: '/admin/employers',
        exact: true,
        component: AdminEmployers,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Сотрудники',
        }
    },
    {
        path: '/admin/employers/new',
        component: AdminAddEmployer,
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новый сотрудник',
        }
    },
    {
        path: '/admin/employers/:id',
        component: AdminEditEmployer,
        exact: true,
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
        component: AdminPositions,
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Должности',
        }
    },
    {
        path: '/admin/directory/positions/new',
        component: AdminAddPositions,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новая должность',
        }
    },
    {
        path: '/admin/directory/positions/:id',
        component: AdminEditPositions,
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Редактирование должности',
        }
    },
    {
        path: '/admin/directory/advertising_types',
        component: AdminAdvertisingTypes,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Виды рекламы',
        }
    },
    {
        path: '/admin/directory/advertising_types/new',
        component: AdminAddAdvertisingTypes,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новый вид рекламы',
        }
    },
    {
        path: '/admin/directory/advertising_types/:id',
        component: AdminEditAdvertisingTypes,
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Редактирование вида рекламы',
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
    const DEFAULT_TITLE = auth.user?.company_name || 'АВТОПРОКАТ';

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