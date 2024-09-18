<script setup>
import {RouterLink} from "vue-router";
import {ref} from "vue";
import { onClickOutside } from '@vueuse/core'
import { useAuthStore, useSidebarStore } from '@stores';

const {user} = useAuthStore();

import SidebarItem from "@components/Containers/Admin/Sidebar/SidebarItem.vue";
import Logo from "@assets/images/logo.png";

const target = ref(null);
const menuGroups = ref([
  {
    //Иконки взяты из https://primevue.org/icons/#list
    name: 'МЕНЮ',
    menuItems: [
      {
        icon: `pi pi-home`,
        label: 'Главная',
        route: '/Admin/',
        visible: true
      },
      {
        icon: `pi pi-sitemap`,
        label: 'Компания',
        route: '',
        children: [
          { label: 'Сотрудники', route: '/Admin/employers', visible: user.access?.employers && user.access.employers !== 0 },
        ],
        visible: true
      },
      {
        icon: `pi pi-users`,
        label: 'Клиенты',
        route: '',
        children: [
          { label: 'Юрлица', route: '/Admin/legalPerson', visible: user.access?.legal_person !== 0 },
        ],
        visible: user.access?.legal_person && user.access?.legal_person !== 0
      },
      {
        icon: `pi pi-folder-open`,
        label: 'Справочники',
        route: '',
        children: [
          { label: 'Виды рекламы', route: '/Admin/directory/advertising_types', visible: true },
          { label: 'Должности', route: '/Admin/directory/positions', visible: true }
        ],
        visible: user.access?.directory && user.access.directory !== 0
      }
    ]
  },
])

const sidebarStore = useSidebarStore();
const authStore = useAuthStore();

onClickOutside(target, () => {
  sidebarStore.isSidebarOpen = false
});
</script>

<template>
  <aside
      class="absolute left-0 top-0 z-9999 flex h-screen w-72.5 flex-col overflow-y-hidden bg-boxdark duration-300 ease-linear dark:bg-boxdark lg:static lg:translate-x-0"
      :class="{
      'translate-x-0': sidebarStore.isSidebarOpen,
      '-translate-x-full': !sidebarStore.isSidebarOpen
    }"
      ref="target"
  >
    <!-- SIDEBAR HEADER -->
    <div class="flex items-center justify-center gap-2 px-6 py-5.5 lg:py-6.5">
      <div class="flex flex-col items-center justify-center">
        <img
            :src="Logo"
            class="mr-3 h-12"
            alt="Логотип компании"
        />
        <div>
          <span class="block self-center text-2xl font-semibold whitespace-normal text-white">{{ authStore.user.company_name }}</span>
          <!--              <span class="block self-center text-sm font-semibold whitespace-nowrap text-gray-300 dark:text-gray-500">АВТОПРОКАТ</span>-->
        </div>
      </div>
    </div>
    <!-- SIDEBAR HEADER -->

    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
      <!-- Sidebar Menu -->
      <nav class="mt-2 py-4 px-4 lg:mt-4 lg:px-6">
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
      <!-- Sidebar Menu -->
    </div>
  </aside>
</template>

<style scoped>
.router-link{
  @apply flex items-center p-2 text-base font-medium text-primary-900 rounded-lg dark:text-white hover:bg-primary-300 dark:hover:bg-primary-600;
}

.router-link-exact-active{
  @apply bg-primary-400 dark:bg-primary-700;
}
</style>