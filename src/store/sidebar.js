import { useStorage } from '@vueuse/core'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSidebarStore = defineStore('sidebar', () => {
  const isSidebarOpen = ref(false)
  const selected = useStorage('selected', ref('Дашборд'))
  const page = useStorage('page', ref('Дашборд'))

  function toggleSidebar() {
    isSidebarOpen.value = !isSidebarOpen.value
  }

  function setSidebarState(state) {
    isSidebarOpen.value = state
  }

  return { isSidebarOpen, setSidebarState, toggleSidebar, selected, page }
})
