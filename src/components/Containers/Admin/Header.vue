<script setup>
import {useAuthStore} from "@stores";

import Logo from "@assets/images/logo.png";
import {useSidebarStore} from "@stores/sidebar.js";
import {RouterLink} from "vue-router";
import DarkModeSwitcher from "@components/DarkModeSwitcher.vue";
import DropdownUser from "@components/DropdownUser.vue";


const authStore = useAuthStore();
const sidebarStore = useSidebarStore();
const { toggleSidebar } = useSidebarStore();
</script>

<template>
  <header
      class="sticky top-0 z-50 flex w-full bg-white drop-shadow-sm dark:bg-boxdark dark:drop-shadow-none"
  >
    <div class="flex flex-grow items-center justify-between py-4.5 px-4.5 shadow-md md:px-6 2xl:px-11">
      <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
        <!-- Hamburger Toggle BTN -->
        <button
            class="z-99999 block rounded-sm border border-stroke bg-white p-1.5 shadow-sm dark:border-strokedark dark:bg-boxdark lg:hidden"
            @click="toggleSidebar"
        >
          <span class="relative block h-5.5 w-5.5 cursor-pointer">
            <span class="block absolute right-0 h-full w-full">
              <span
                  class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-[0] duration-200 ease-in-out dark:bg-white"
                  :class="{ '!w-full delay-300': !sidebarStore.isSidebarOpen }"
              ></span>
              <span
                  class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-150 duration-200 ease-in-out dark:bg-white"
                  :class="{ '!w-full delay-400': !sidebarStore.isSidebarOpen }"
              ></span>
              <span
                  class="relative top-0 left-0 my-1 block h-0.5 w-0 rounded-sm bg-black delay-200 duration-200 ease-in-out dark:bg-white"
                  :class="{ '!w-full delay-500': !sidebarStore.isSidebarOpen }"
              ></span>
            </span>
            <span class="block absolute right-0 h-full w-full rotate-45">
              <span
                  class="absolute left-2.5 top-0 block h-full w-0.5 rounded-sm bg-black delay-300 duration-200 ease-in-out dark:bg-white"
                  :class="{ '!h-0 delay-[0]': !sidebarStore.isSidebarOpen }"
              ></span>
              <span
                  class="delay-400 absolute left-0 top-2.5 block h-0.5 w-full rounded-sm bg-black duration-200 ease-in-out dark:bg-white"
                  :class="{ '!h-0 delay-200': !sidebarStore.isSidebarOpen }"
              ></span>
            </span>
          </span>
        </button>
        <!-- Hamburger Toggle BTN -->
        <router-link :to="{name: 'Index'}" class="flex items-center gap-2 lg:hidden">
          <img
              :src="Logo"
              class="h-12 min-h-12 min-w-12"
              alt="Логотип компании"
          />
          <div>
            <span class="hidden sm:block self-center text-2xl font-semibold whitespace-nowrap text-gray-800 dark:text-white">{{ authStore.user.company_name }}</span>
            <!--              <span class="block self-center text-sm font-semibold whitespace-nowrap text-gray-300 dark:text-gray-500">АВТОПРОКАТ</span>-->
          </div>
        </router-link>
      </div>

      <div class="hidden sm:block"></div>

      <div class="flex items-center gap-3 2xsm:gap-7">
        <ul class="flex items-center gap-2 2xsm:gap-4">
          <li>
            <DarkModeSwitcher />
          </li>
        </ul>

        <DropdownUser />
      </div>
    </div>
  </header>
</template>

<style scoped>

</style>