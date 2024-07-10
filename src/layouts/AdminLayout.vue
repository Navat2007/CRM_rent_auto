<script setup>
import {ref} from "vue";
import {RouterLink} from "vue-router";

import {useAuthStore} from "@stores";

import Logo from "@assets/images/logo.png";
import AlertModal from "@components/Modals/AlertModal.vue";
import DarkToggle from "@components/DarkToggle.vue";

const isLogoutModalOpen = ref(false);

const handleLogout = async () => {
  const authStore = useAuthStore();
  await authStore.logout();
}
</script>

<template>
  <div class="antialiased bg-gray-50 dark:bg-gray-900">
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
      <div class="flex flex-wrap justify-between items-center">
        <!-- Logo -->
        <div class="flex justify-start items-center">
          <button
              data-drawer-target="drawer-navigation"
              data-drawer-toggle="drawer-navigation"
              aria-controls="drawer-navigation"
              class="p-2 mr-2 text-gray-600 rounded-lg cursor-pointer md:hidden hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
          >
            <svg
                aria-hidden="true"
                class="w-6 h-6"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
            >
              <path
                  fill-rule="evenodd"
                  d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                  clip-rule="evenodd"
              ></path>
            </svg>
            <svg
                aria-hidden="true"
                class="hidden w-6 h-6"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
            >
              <path
                  fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"
              ></path>
            </svg>
            <span class="sr-only">Toggle sidebar</span>
          </button>
          <router-link :to="{name: 'Index'}" class="flex items-center justify-between mr-4">
            <img
                :src="Logo"
                class="mr-3 h-12"
                alt="Bee Cars Logo"
            />
            <div>
              <span class="block self-center text-2xl font-semibold whitespace-nowrap text-gray-800 dark:text-white">БИ КАРС</span>
              <span class="block self-center text-sm font-semibold whitespace-nowrap text-gray-300 dark:text-gray-500">АВТОПРОКАТ</span>
            </div>
          </router-link>
        </div>
        <div class="flex items-center lg:order-2">
          <DarkToggle/>
          <button
              @click="isLogoutModalOpen = true"
              type="button"
              data-dropdown-toggle="notification-dropdown"
              class="p-2 mr-1 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
          >
            <span class="sr-only">Logout</span>
            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2"/>
            </svg>
          </button>
        </div>
      </div>
    </nav>

    <!-- Sidebar -->
    <aside
        class="fixed top-0 left-0 z-40 w-64 h-screen pt-14 transition-transform -translate-x-full bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
        aria-label="Sidenav"
        id="drawer-navigation"
    >
      <div class="overflow-y-auto py-5 px-3 h-full bg-white dark:bg-gray-800">
        <form action="#" method="GET" class="hidden mb-2">
          <label for="sidebar-search" class="sr-only">Search</label>
          <div class="relative">
            <div
                class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none"
            >
              <svg
                  class="w-5 h-5 text-gray-500 dark:text-gray-400"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                  xmlns="http://www.w3.org/2000/svg"
              >
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                ></path>
              </svg>
            </div>
            <input
                type="text"
                name="search"
                id="sidebar-search"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                placeholder="Search"
            />
          </div>
        </form>
        <ul class="space-y-2">
          <li>
            <router-link
                :to="{ name: 'AdminCompanies' }"
                class="router-link"
            >
              <span class="ml-3">Компании</span>
            </router-link>
          </li>
          <li>
            <router-link
                :to="{ name: 'AdminUsers' }"
                class="router-link"
            >              
              <span class="ml-3">Пользователи</span>
            </router-link>
          </li>
        </ul> 
      </div>
    </aside>

    <!-- Main -->
    <main class="md:ml-64 h-screen pt-16">
      <slot />
    </main>
  </div>

  <AlertModal
      target="#modal"
      :isOpen="isLogoutModalOpen"
      @close="isLogoutModalOpen = false"
      @accept="handleLogout"
      title="Вы действительно хотите выйти?"
      withButtons info
  />
</template>

<style scoped>
.router-link{
  @apply flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-secondary-300 dark:hover:bg-secondary-600;
}

.router-link-exact-active{
  @apply bg-secondary-400 dark:bg-secondary-700;
}
</style>