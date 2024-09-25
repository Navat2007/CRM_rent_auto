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
import AdminEmployers from "@pages/admin/employers/Employers.vue";
import AdminAddEmployer from "@pages/admin/employers/AddEmployer.vue";
import AdminEditEmployer from "@pages/admin/employers/EditEmployer.vue";
import AdminLegalPerson from "@pages/admin/legalPerson/LegalPerson.vue";
import AdminAddLegalPerson from "@pages/admin/legalPerson/AddLegalPerson.vue";
import AdminEditLegalPerson from "@pages/admin/legalPerson/EditLegalPerson.vue";
import AdminClients from "@pages/admin/clients/Clients.vue";
import AdminAddClient from "@pages/admin/clients/AddClient.vue";
import AdminEditClient from "@pages/admin/clients/EditClient.vue";
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
    // Employers
    {
        path: '/Admin/employers',
        exact: true,
        component: AdminEmployers,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Сотрудники',
        }
    },
    {
        path: '/Admin/employers/new',
        component: AdminAddEmployer,
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новый сотрудник',
        }
    },
    {
        path: '/Admin/employers/:id',
        component: AdminEditEmployer,
        exact: true,
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Редактирование сотрудника',
        }
    },
    //Clients
    {
        path: '/Admin/legalPerson',
        exact: true,
        component: AdminLegalPerson,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Юр лица',
        }
    },
    {
        path: '/Admin/legalPerson/new',
        component: AdminAddLegalPerson,
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новое юр лицо',
        }
    },
    {
        path: '/Admin/legalPerson/:id',
        component: AdminEditLegalPerson,
        exact: true,
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Редактирование юр лица',
        }
    },
    {
        path: '/Admin/clients',
        exact: true,
        component: AdminClients,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Клиенты',
        }
    },
    {
        path: '/Admin/clients/new',
        component: AdminAddClient,
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новый клиент',
        }
    },
    {
        path: '/Admin/clients/:id',
        component: AdminEditClient,
        exact: true,
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Редактирование клиента',
        }
    },
    // Directory
    {
        path: '/Admin/directory/positions',
        component: AdminPositions,
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Должности',
        }
    },
    {
        path: '/Admin/directory/positions/new',
        component: AdminAddPositions,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новая должность',
        }
    },
    {
        path: '/Admin/directory/positions/:id',
        component: AdminEditPositions,
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Редактирование должности',
        }
    },
    {
        path: '/Admin/directory/advertising_types',
        component: AdminAdvertisingTypes,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Виды рекламы',
        }
    },
    {
        path: '/Admin/directory/advertising_types/new',
        component: AdminAddAdvertisingTypes,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Новый вид рекламы',
        }
    },
    {
        path: '/Admin/directory/advertising_types/:id',
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
    routes,
    scrollBehavior(to, from, savedPosition) {
        return savedPosition || { left: 0, top: 0 }
    }
});

router.beforeEach((to, from, next) => {
    const auth = useAuthStore();
    const { setSidebarState } = useSidebarStore();
    const DEFAULT_TITLE = auth.user?.company_name || 'АВТОПРОКАТ';

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