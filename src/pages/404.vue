<script setup lang="ts">
import {RouterLink} from "vue-router";
import DarkToggle from "@components/DarkToggle.vue";
</script>

<template>
  <section class="h-screen w-screen bg-white dark:bg-gray-900 flex justify-center items-center">
    <div class="absolute top-0 right-0 flex items-end justify-center pr-2 pt-2">
      <DarkToggle />
    </div>

    <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
      <div class="mx-auto max-w-screen-sm text-center">
        <h1 class="mb-4 text-7xl tracking-tight font-extrabold lg:text-9xl text-primary-600 dark:text-primary-500">404</h1>
        <p class="mb-4 text-3xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white">Что-то пошло не так</p>
        <p class="mb-4 text-lg font-light text-gray-500 dark:text-gray-400">Извините, не удалось найти запрашиваемую страницу.</p>
        <router-link :to="{name: 'Index'}"
           class="inline-flex text-white bg-primary-600 hover:bg-primary-800 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:focus:ring-primary-900 my-4"
        >
          Вернуться на главную
        </router-link>
      </div>
    </div>
  </section>
</template>