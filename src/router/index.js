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
import AdminAuto from "@pages/admin/auto/List.vue";
import AdminAddAuto from "@pages/admin/auto/Add.vue";
import AdminEditAuto from "@pages/admin/auto/Edit.vue";

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
    // Clients
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
    // Auto
    {
        path: '/Admin/auto',
        exact: true,
        component: AdminAuto,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто',
        }
    },
    {
        path: '/Admin/auto/new',
        component: AdminAddAuto,
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто',
        }
    },
    {
        path: '/Admin/auto/:id',
        component: AdminEditAuto,
        exact: true,
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто',
        }
    },
    // Booking
    {
        path: '/Admin/booking/rentalContracts',
        exact: true,
        component: () => import('@pages/admin/booking/rentalContracts/List.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Договора проката',
        }
    },
    {
        path: '/Admin/booking/rentalContracts/new',
        component: () => import('@pages/admin/booking/rentalContracts/Add.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Договора проката',
        }
    },
    {
        path: '/Admin/booking/rentalContracts/:id',
        component: () => import('@pages/admin/booking/rentalContracts/Edit.vue'),
        exact: true,
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Договора проката',
        }
    },
    {
        path: '/Admin/booking/rentalCalendar',
        exact: true,
        component: () => import('@pages/admin/booking/rentalCalendar/List.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Календарь занятости авто',
        }
    },
    // Directory
    {
        path: '/Admin/directory/position',
        component: () => import('@pages/admin/directory/position/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Должности',
        }
    },
    {
        path: '/Admin/directory/position/new',
        component: () => import('@pages/admin/directory/position/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Должности',
        }
    },
    {
        path: '/Admin/directory/position/:id',
        component: () => import('@pages/admin/directory/position/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Должности',
        }
    },
    {
        path: '/Admin/directory/advertising_types',
        component: () => import('@pages/admin/directory/advertising_types/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Виды рекламы',
        }
    },
    {
        path: '/Admin/directory/advertising_types/new',
        component: () => import('@pages/admin/directory/advertising_types/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Виды рекламы',
        }
    },
    {
        path: '/Admin/directory/advertising_types/:id',
        component: () => import('@pages/admin/directory/advertising_types/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Виды рекламы',
        }
    },
    {
        path: '/Admin/directory/car_bodies',
        component: () => import('@pages/admin/directory/car_bodies/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Тип кузова',
        }
    },
    {
        path: '/Admin/directory/car_bodies/new',
        component: () => import('@pages/admin/directory/car_bodies/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Тип кузова',
        }
    },
    {
        path: '/Admin/directory/car_bodies/:id',
        component: () => import('@pages/admin/directory/car_bodies/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Тип кузова',
        }
    },
    {
        path: '/Admin/directory/car_brands',
        component: () => import('@pages/admin/directory/car_brands/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Марка',
        }
    },
    {
        path: '/Admin/directory/car_brands/new',
        component: () => import('@pages/admin/directory/car_brands/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Марка',
        }
    },
    {
        path: '/Admin/directory/car_brands/:id',
        component: () => import('@pages/admin/directory/car_brands/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Марка',
        }
    },
    {
        path: '/Admin/directory/car_classes',
        component: () => import('@pages/admin/directory/car_classes/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Класс',
        }
    },
    {
        path: '/Admin/directory/car_classes/new',
        component: () => import('@pages/admin/directory/car_classes/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Класс',
        }
    },
    {
        path: '/Admin/directory/car_classes/:id',
        component: () => import('@pages/admin/directory/car_classes/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Класс',
        }
    },
    {
        path: '/Admin/directory/car_classes_service_price',
        component: () => import('@pages/admin/directory/car_classes_service_price/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Классы и цены на услуги',
        }
    },
    {
        path: '/Admin/directory/car_classes_service_price/new',
        component: () => import('@pages/admin/directory/car_classes_service_price/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Классы и цены на услуги',
        }
    },
    {
        path: '/Admin/directory/car_classes_service_price/:id',
        component: () => import('@pages/admin/directory/car_classes_service_price/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Классы и цены на услуги',
        }
    },
    {
        path: '/Admin/directory/car_colors',
        component: () => import('@pages/admin/directory/car_colors/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Цвет',
        }
    },
    {
        path: '/Admin/directory/car_colors/new',
        component: () => import('@pages/admin/directory/car_colors/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Цвет',
        }
    },
    {
        path: '/Admin/directory/car_colors/:id',
        component: () => import('@pages/admin/directory/car_colors/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Цвет',
        }
    },
    {
        path: '/Admin/directory/car_configurations',
        component: () => import('@pages/admin/directory/car_configurations/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Комплектация',
        }
    },
    {
        path: '/Admin/directory/car_configurations/new',
        component: () => import('@pages/admin/directory/car_configurations/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Комплектация',
        }
    },
    {
        path: '/Admin/directory/car_configurations/:id',
        component: () => import('@pages/admin/directory/car_configurations/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Комплектация',
        }
    },
    {
        path: '/Admin/directory/car_fuel_types',
        component: () => import('@pages/admin/directory/car_fuel_types/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Тип топлива',
        }
    },
    {
        path: '/Admin/directory/car_fuel_types/new',
        component: () => import('@pages/admin/directory/car_fuel_types/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Тип топлива',
        }
    },
    {
        path: '/Admin/directory/car_fuel_types/:id',
        component: () => import('@pages/admin/directory/car_fuel_types/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Тип топлива',
        }
    },
    {
        path: '/Admin/directory/car_generations',
        component: () => import('@pages/admin/directory/car_generations/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Поколение',
        }
    },
    {
        path: '/Admin/directory/car_generations/new',
        component: () => import('@pages/admin/directory/car_generations/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Поколение',
        }
    },
    {
        path: '/Admin/directory/car_generations/:id',
        component: () => import('@pages/admin/directory/car_generations/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Поколение',
        }
    },
    {
        path: '/Admin/directory/car_interior_materia',
        component: () => import('@pages/admin/directory/car_interior_materia/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Материал салона',
        }
    },
    {
        path: '/Admin/directory/car_interior_materia/new',
        component: () => import('@pages/admin/directory/car_interior_materia/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Материал салона',
        }
    },
    {
        path: '/Admin/directory/car_interior_materia/:id',
        component: () => import('@pages/admin/directory/car_interior_materia/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Материал салона',
        }
    },
    {
        path: '/Admin/directory/car_models',
        component: () => import('@pages/admin/directory/car_models/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Модель',
        }
    },
    {
        path: '/Admin/directory/car_models/new',
        component: () => import('@pages/admin/directory/car_models/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Модель',
        }
    },
    {
        path: '/Admin/directory/car_models/:id',
        component: () => import('@pages/admin/directory/car_models/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Модель',
        }
    },
    {
        path: '/Admin/directory/car_pts_ownership',
        component: () => import('@pages/admin/directory/car_pts_ownership/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: ПТС Форма собственности',
        }
    },
    {
        path: '/Admin/directory/car_pts_ownership/new',
        component: () => import('@pages/admin/directory/car_pts_ownership/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: ПТС Форма собственности',
        }
    },
    {
        path: '/Admin/directory/car_pts_ownership/:id',
        component: () => import('@pages/admin/directory/car_pts_ownership/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: ПТС Форма собственности',
        }
    },
    {
        path: '/Admin/directory/car_statuses',
        component: () => import('@pages/admin/directory/car_statuses/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Статус',
        }
    },
    {
        path: '/Admin/directory/car_statuses/new',
        component: () => import('@pages/admin/directory/car_statuses/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Статус',
        }
    },
    {
        path: '/Admin/directory/car_statuses/:id',
        component: () => import('@pages/admin/directory/car_statuses/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Статус',
        }
    },
    {
        path: '/Admin/directory/car_tires_type',
        component: () => import('@pages/admin/directory/car_tires_type/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Резина',
        }
    },
    {
        path: '/Admin/directory/car_tires_type/new',
        component: () => import('@pages/admin/directory/car_tires_type/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Резина',
        }
    },
    {
        path: '/Admin/directory/car_tires_type/:id',
        component: () => import('@pages/admin/directory/car_tires_type/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Резина',
        }
    },
    {
        path: '/Admin/directory/car_transmissions',
        component: () => import('@pages/admin/directory/car_transmissions/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Трансмиссия',
        }
    },
    {
        path: '/Admin/directory/car_transmissions/new',
        component: () => import('@pages/admin/directory/car_transmissions/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Трансмиссия',
        }
    },
    {
        path: '/Admin/directory/car_transmissions/:id',
        component: () => import('@pages/admin/directory/car_transmissions/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Трансмиссия',
        }
    },
    {
        path: '/Admin/directory/car_wheel_drive',
        component: () => import('@pages/admin/directory/car_wheel_drive/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Привод',
        }
    },
    {
        path: '/Admin/directory/car_wheel_drive/new',
        component: () => import('@pages/admin/directory/car_wheel_drive/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Привод',
        }
    },
    {
        path: '/Admin/directory/car_wheel_drive/:id',
        component: () => import('@pages/admin/directory/car_wheel_drive/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Привод',
        }
    },
    {
        path: '/Admin/directory/car_wheel_size',
        component: () => import('@pages/admin/directory/car_wheel_size/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Размер колес',
        }
    },
    {
        path: '/Admin/directory/car_wheel_size/new',
        component: () => import('@pages/admin/directory/car_wheel_size/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Размер колес',
        }
    },
    {
        path: '/Admin/directory/car_wheel_size/:id',
        component: () => import('@pages/admin/directory/car_wheel_size/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Авто: Размер колес',
        }
    },
    {
        path: '/Admin/directory/services',
        component: () => import('@pages/admin/directory/services/List.vue'),
        exact: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Услуги при бронировании',
        }
    },
    {
        path: '/Admin/directory/services/new',
        component: () => import('@pages/admin/directory/services/Add.vue'),
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Услуги при бронировании',
        }
    },
    {
        path: '/Admin/directory/services/:id',
        component: () => import('@pages/admin/directory/services/Edit.vue'),
        props: true,
        meta: {
            layout: AdminLayout,
            requiresAuth: true,
            title: 'Услуги при бронировании',
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
        if (!auth.user || !auth.user.access) {
            next({path: '/login'});
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;