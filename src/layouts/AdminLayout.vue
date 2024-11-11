<script setup>
import Header from "@components/Containers/Admin/Header.vue";
import Sidebar from "@components/Containers/Admin/Sidebar/Sidebar.vue";

</script>

<template>
  <div class="flex h-screen overflow-hidden">
    <Sidebar />

    <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
      <Header />

      <main>
        <div class="mx-auto p-4 md:p-6 2xl:p-10">
          <slot></slot>
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>

</style>