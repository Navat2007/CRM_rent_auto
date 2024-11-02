import {createRouter, createWebHistory} from 'vue-router';
import { useAuthStore, useSidebarStore } from '@stores';

import PublicLayout from "@layouts/PublicLayout.vue";
import AdminLayout from "@layouts/AdminLayout.vue";

// Public routes
import Index from "@pages/public/Index.vue";
import Login from "@pages/Login.vue";
import NotFound from "@pages/404.vue";

// Admin routes
import Admin from "@pages/admin/Admin.vue";
import AdminProfile from "@pages/admin/Profile.vue";
import AdminSchoolEvents from "@pages/admin/school/events/Events.vue";
import AdminSchoolAddEvent from "@pages/admin/school/events/AddEvent.vue";
import AdminSchoolEditEvent from "@pages/admin/school/events/EditEvent.vue";

const publicRoutes = [
    {
        path: '/login',
        name: 'Login',
        component: Login,
    },
    {
        path: '/',
        name: 'Index',
        component: Index,
        meta: {
            layout: PublicLayout,
        }
    },
];

const adminRoutes = [
    {
        path: '/Admin',
        name: 'Admin',
        exact: true,
        component: Admin,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
        }
    },
    {
        path: '/Admin/profile',
        exact: true,
        component: AdminProfile,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
        }
    },
    // --- School ---
    // Events
    {
        path: '/Admin/school/events',
        exact: true,
        component: AdminSchoolEvents,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Мероприятия',
        }
    },
    {
        path: '/Admin/school/events/new',
        component: AdminSchoolAddEvent,
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новые мероприятие',
        }
    },
    {
        path: '/Admin/school/events/:id',
        component: AdminSchoolEditEvent,
        exact: true,
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Редактирование мероприятия',
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
    routes,
    scrollBehavior(to, from, savedPosition) {
        return savedPosition || { left: 0, top: 0 }
    }
});

router.beforeEach((to, from, next) => {
    const auth = useAuthStore();
    const { setSidebarState } = useSidebarStore();
    const DEFAULT_TITLE = auth.user?.school?.org_short_name ? 'Административная панель - ' + auth.user?.school?.org_short_name : 'Административная панель МЦВП';

    document.title = to.meta.title || DEFAULT_TITLE;

    setSidebarState(false);

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