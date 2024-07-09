<script setup>
import {ref, onBeforeMount, onMounted} from "vue";
import {RouterLink} from "vue-router";
import Logo from "@assets/images/logo.svg";

const appButton = ref();
const userScroll = () => {
  if (window.scrollY > 0) {
    appButton.value.classList.add("showButton");
  } else {
    appButton.value.classList.remove("showButton");
  }
};

onMounted(() => {
  window.addEventListener("scroll", userScroll);
});
onBeforeMount(() => {
  window.removeEventListener("scroll", userScroll);
});

const scrollToTop = () => {
  window.scrollTo({top: 0, behavior: "smooth"});
}
</script>

<template>
  <div class="h-screen bg-gray-50 dark:bg-gray-900">
    <div class="antialiased bg-gray-50 dark:bg-gray-900">
      <nav class="nav">
        <div class="flex flex-wrap justify-between items-center">
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
            <RouterLink to="/" class="flex items-center justify-between mr-4">
              <img
                  :src="Logo"
                  class="mr-3 h-12"
                  alt="Sova Games Logo"
              />
              <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Sova Games</span>
            </RouterLink>
          </div>
          <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <li class="me-2">
              <RouterLink to="/" class="routerLink">Релизы</RouterLink>
            </li>
            <li class="me-2">
              <RouterLink to="/development/" class="routerLink">В разработке</RouterLink>
            </li>
            <li class="me-2">
              <RouterLink to="/prototypes/" class="routerLink">Прототипы</RouterLink>
            </li>
            <li class="me-2">
              <RouterLink to="/contacts/" class="routerLink">Контакты</RouterLink>
            </li>
          </ul>
          <div class="flex items-center lg:order-2">
            <RouterLink :to="{ name: 'AdminUsers' }">
              <button
                  type="button"
                  data-dropdown-toggle="notification-dropdown"
                  class="p-2 mr-1 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
              >
                <span class="sr-only">View notifications</span>
                <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                     width="24" height="24" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 12H4m12 0-4 4m4-4-4-4m3-4h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-2"/>
                </svg>
              </button>
            </RouterLink>
          </div>
        </div>
      </nav>

      <!-- Main -->
      <main class="p-8 h-auto ">
        <slot/>
      </main>
    </div>
  </div>
  <button
      :onclick="scrollToTop" ref="appButton"
      type="button"
      data-twe-ripple-init
      data-twe-ripple-color="light"
      class="!fixed bottom-5 end-5 hidden rounded-full bg-red-600 p-3 text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg"
  >
    <span class="[&>svg]:w-4">
      <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="3"
          stroke="currentColor">
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18"/>
      </svg>
    </span>
  </button>
</template>

<style scoped>
.nav {
  @apply bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 left-0 right-0 top-0 z-50;
}

.routerLink {
  @apply inline-block p-4 rounded-t-lg dark:text-amber-100;

  &:hover {
    @apply border-b-2 border-amber-500;
    @apply dark:border-amber-500;
  }

  &.router-link-exact-active {
    @apply dark:text-amber-100;
    @apply border-b-2 border-amber-500;
    @apply dark:border-amber-500;
  }
}
</style>