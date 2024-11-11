import { useStorage } from '@vueuse/core'
import { defineStore } from 'pinia'
import {ref} from 'vue'
import {useRoute} from "vue-router";

export const useSidebarStore = defineStore('sidebar', () => {
  const isSidebarOpen = ref(false);
  const isSidebarFull = ref(true);
  const selected = useStorage('selected', ref('Дашборд'));
  const page = useStorage('page', ref('Дашборд'));
  const route = useRoute();

  function toggleSidebar() {
    isSidebarOpen.value = !isSidebarOpen.value;
  }

  function setSidebarState(state) {
    isSidebarOpen.value = state;
  }

  function setSidebarFull(state) {
    isSidebarFull.value = state;
  }

  function toggleSidebarFull() {
    isSidebarFull.value = !isSidebarFull.value;
  }

  return { isSidebarOpen, isSidebarFull, setSidebarState, setSidebarFull, toggleSidebar, toggleSidebarFull, selected, page }
})
