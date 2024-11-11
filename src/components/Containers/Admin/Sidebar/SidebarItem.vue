<script setup lang="ts">
import {useSidebarStore} from '@stores/sidebar'
import {useRoute} from 'vue-router'
import {Icon} from '@vicons/utils'
import SidebarDropdown from './SidebarDropdown.vue'

const sidebarStore = useSidebarStore()

const props = defineProps(['item', 'index'])
const currentPage = useRoute().name

interface SidebarItem {
  label: string
}

const handleItemClick = () => {
  sidebarStore.page = sidebarStore.page === props.item.label ? '' : props.item.label
  sidebarStore.selected = sidebarStore.selected === props.item.label ? '' : props.item.label

  if (props.item.children) {
    return props.item.children.some((child: SidebarItem) => sidebarStore.selected === child.label)
  }
}
</script>

<template>
  <li v-if="props.item.visible">
    <router-link
        :to="item.route"
        class="group relative flex items-center gap-2.5 rounded-sm py-2 font-medium text-primary-50 duration-300 ease-in-out hover:bg-primary-500"
        @click.prevent="handleItemClick"
        :class="{
        'bg-primary-700': sidebarStore.page === item.label || sidebarStore.selected === item.label,
        'px-8': sidebarStore.isSidebarFull,
        'px-6.5': !sidebarStore.isSidebarFull,
      }"
    >
      <Icon size="24">
        <component :is="item.icon"/>
      </Icon>

      <Transition name="fade">
        <span v-if="sidebarStore.isSidebarFull">{{ item.label }}</span>
      </Transition>

      <Transition name="fade">
        <svg
            v-if="sidebarStore.isSidebarFull && item.children"
            class="absolute right-4 top-1/2 -translate-y-1/2 fill-current"
            :class="{ 'rotate-180': sidebarStore.page === item.label }"
            width="20"
            height="20"
            viewBox="0 0 20 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
          <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M4.41107 6.9107C4.73651 6.58527 5.26414 6.58527 5.58958 6.9107L10.0003 11.3214L14.4111 6.91071C14.7365 6.58527 15.2641 6.58527 15.5896 6.91071C15.915 7.23614 15.915 7.76378 15.5896 8.08922L10.5896 13.0892C10.2641 13.4147 9.73651 13.4147 9.41107 13.0892L4.41107 8.08922C4.08563 7.76378 4.08563 7.23614 4.41107 6.9107Z"
              fill=""
          />
        </svg>
      </Transition>
    </router-link>

    <!-- Dropdown Menu Start -->
    <div v-if="sidebarStore.isSidebarFull" class="translate transform overflow-hidden"
         v-show="sidebarStore.page === item.label">
      <SidebarDropdown
          v-if="item.children"
          :items="item.children"
          :currentPage="currentPage"
          :page="item.label"
      />
      <!-- Dropdown Menu End -->
    </div>
  </li>
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
</style>