<script setup>
import {ref, markRaw, onMounted} from "vue";
import {onClickOutside} from '@vueuse/core';
import {useAuthStore, useSidebarStore} from '@stores';
import {Icon} from '@vicons/utils'
import {Home, Building, Group, Car, TrafficEvent, Folders, ArrowLeft, EventsAlt} from '@vicons/carbon';

import SidebarItem from "@components/Containers/Admin/Sidebar/SidebarItem.vue";
import Logo from "@assets/images/logo.png";

const {user} = useAuthStore();

const showLogo = ref(true);
const target = ref(null);
const menuGroups = ref([
    {
        //Иконки взяты из https://primevue.org/icons/#list
        //-> Иконки взяты из https://www.xicons.org/#/
        name: 'МЕНЮ',
        menuItems: [
            {
                icon: markRaw(Home),
                label: 'Главная',
                route: '/Admin/',
                visible: true
            },
            {
                icon: markRaw(Building),
                label: 'Компания',
                route: '',
                children: [
                    {
                        label: 'Сотрудники',
                        route: '/Admin/employers',
                        visible: user.access?.employers && user.access.employers !== 0
                    },
                    {
                        label: 'Шаблоны договоров',
                        route: '/Admin/contractsTemplate',
                        visible: true
                    },
                ],
                visible: user.access?.employers && user.access.employers !== 0
            },
            {
                icon: markRaw(EventsAlt),
                label: 'Юрлица',
                route: '/Admin/legalPerson',
                visible: user.access?.clients && user.access.clients !== 0
            },
            {
                icon: markRaw(Group),
                label: 'Клиенты',
                route: '/Admin/clients',
                visible: user.access?.clients && user.access?.clients !== 0
            },
            {
                icon: markRaw(Car),
                label: 'Авто',
                route: '/Admin/auto',
                visible: user.access?.auto && user.access?.auto !== 0
            },
            {
                icon: markRaw(TrafficEvent),
                label: 'Бронирование',
                route: '',
                children: [
                    {
                        label: 'Договора проката',
                        route: '/Admin/booking/rentalContracts',
                        visible: user.access?.booking && user.access.booking !== 0
                    },
                    {
                        label: 'Календарь занятости авто',
                        route: '/Admin/booking/rentalCalendar',
                        visible: user.access?.booking && user.access.booking !== 0
                    },
                ],
                visible: user.access?.booking && user.access?.booking !== 0
            },
            {
                icon: markRaw(Folders),
                label: 'Справочники',
                route: '',
                children: [
                    {label: 'Виды рекламы', route: '/Admin/directory/advertising_types', visible: true},
                    {label: 'Должности', route: '/Admin/directory/position', visible: true},
                    {label: 'Авто: Тип кузова', route: '/Admin/directory/car_bodies', visible: true},
                    {label: 'Авто: Марка', route: '/Admin/directory/car_brands', visible: true},
                    {label: 'Авто: Класс', route: '/Admin/directory/car_classes', visible: true},
                    // {label: 'Авто: Классы и цены на услуги', route: '/Admin/directory/car_classes_service_price', visible: true},
                    {label: 'Авто: Цвет', route: '/Admin/directory/car_colors', visible: true},
                    {label: 'Авто: Комплектация', route: '/Admin/directory/car_configurations', visible: true},
                    {label: 'Авто: Тип топлива', route: '/Admin/directory/car_fuel_types', visible: true},
                    {label: 'Авто: Поколение', route: '/Admin/directory/car_generations', visible: true},
                    {label: 'Авто: Материал салона', route: '/Admin/directory/car_interior_materia', visible: false},
                    {label: 'Авто: Модель', route: '/Admin/directory/car_models', visible: true},
                    {
                        label: 'Авто: ПТС Форма собственности',
                        route: '/Admin/directory/car_pts_ownership',
                        visible: true
                    },
                    {label: 'Авто: Статус', route: '/Admin/directory/car_statuses', visible: false},
                    {label: 'Авто: Резина', route: '/Admin/directory/car_tires_type', visible: false},
                    {label: 'Авто: Трансмиссия', route: '/Admin/directory/car_transmissions', visible: true},
                    {label: 'Авто: Привод', route: '/Admin/directory/car_wheel_drive', visible: false},
                    {label: 'Авто: Размер колес', route: '/Admin/directory/car_wheel_size', visible: true},
                    {
                        label: 'Авто: Территория использования',
                        route: '/Admin/directory/territory_car_use',
                        visible: true
                    },
                    {label: 'Авто: Ценовые периоды', route: '/Admin/directory/price_periods', visible: true},
                    {label: 'Услуги при бронировании', route: '/Admin/directory/services', visible: true},
                    {label: 'Тип операции', route: '/Admin/directory/operation_types', visible: true},
                    {label: 'Виды оплаты', route: '/Admin/directory/payment_types', visible: true},
                ],
                visible: user.access?.directory && user.access.directory !== 0
            }
        ]
    },
]);

const sidebarStore = useSidebarStore();
const authStore = useAuthStore();

onClickOutside(target, () => {
    sidebarStore.isSidebarOpen = false
});

function toggleSidebar() {
    sidebarStore.toggleSidebarFull();

    if (showLogo.value) {
        showLogo.value = false;
    } else {
        setTimeout(() => {
            showLogo.value = true;
        }, 500);
    }
}

onMounted(() => {
    menuGroups.value[0].menuItems.find(item => item.label === 'Справочники').children.sort((a, b) => a.label.localeCompare(b.label));
})
</script>

<template>
    <aside
        class="absolute left-0 top-0 z-999 flex h-screen flex-col bg-boxdark delay-200 duration-300 ease-linear dark:bg-boxdark lg:static lg:translate-x-0"
        :class="{
        'translate-x-0': sidebarStore.isSidebarOpen,
        '-translate-x-full': !sidebarStore.isSidebarOpen,
        'w-72': sidebarStore.isSidebarFull,
        'w-20': !sidebarStore.isSidebarFull,
      }"
        ref="target"
    >
        <div class="flex flex-col overflow-y-hidden h-full">
            <!-- Sidebar Header -->
            <div class="flex flex-none items-center justify-center mt-8 gap-2 px-6 py-5.5 lg:py-6.5 h-36">
                <div class="flex flex-col items-center justify-center">
                    <Transition name="bounce">
                        <img
                            :src="Logo"
                            alt="Логотип компании"
                        />
                    </Transition>
                    <div class="h-16">
                        <Transition name="fade">
              <span v-if="showLogo"
                    class="mt-4 block self-center text-2xl font-semibold whitespace-normal text-white"
              >
                {{ authStore.user.company_name }}
              </span>
                        </Transition>
                    </div>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <div class="flex flex-auto flex-col duration-300 ease-linear overflow-y-auto">
                <nav class="mt-2 py-4 lg:mt-4 ">
                    <template v-for="menuGroup in menuGroups" :key="menuGroup.name">
                        <div>
                            <ul class="mb-6 flex flex-col gap-1.5">
                                <SidebarItem
                                    v-for="(menuItem, index) in menuGroup.menuItems"
                                    :item="menuItem"
                                    :key="index"
                                    :index="index"
                                />
                            </ul>
                        </div>
                    </template>
                </nav>
            </div>
        </div>

        <!-- Toggle arrow -->
        <div
            class="flex w-full overflow-hidden"
            :class="{
            'justify-end': sidebarStore.isSidebarFull,
            'justify-center':!sidebarStore.isSidebarFull,
          }"
        >
            <Icon
                size="32"
                class="text-primary-500 hover:text-primary-300 m-1 delay-200 duration-300"
                :class="{
            'rotate-0 mr-4': sidebarStore.isSidebarFull,
            'rotate-180':!sidebarStore.isSidebarFull,
          }"
                @click="toggleSidebar"
            >
                <ArrowLeft/>
            </Icon>
        </div>
    </aside>
</template>

<style scoped>
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-active {
    transition: opacity 2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.bounce-enter-active {
    animation: bounce-in 0.5s;
}

.bounce-leave-active {
    animation: bounce-in 0.5s reverse;
}

@keyframes bounce-in {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.25);
    }
    100% {
        transform: scale(1);
    }
}
</style>